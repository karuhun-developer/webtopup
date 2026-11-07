<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    public string $baseUrl;

    public function __construct(
        public ?string $serverKey = null,
        public ?string $clientKey = null,
        public ?bool $isProduction = null,
    ) {
        $settings = app(IntegrationSettings::class);
        $this->serverKey = $this->serverKey ?? $settings->credential('midtrans_server_key');
        $this->clientKey = $this->clientKey ?? $settings->credential('midtrans_client_key');
        $this->isProduction = $this->isProduction ?? (bool) (getSetting('midtrans_is_production') ?? config('midtrans.is_production', false));
        $this->baseUrl = $this->isProduction ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Creates a bank transfer payment transaction through Midtrans API.
     *
     * This method initiates a bank transfer payment by sending a charge request
     * to Midtrans with the specified order details and bank selection.
     *
     * @param  string  $orderId  Unique identifier for the order/transaction
     * @param  int  $amount  Transaction amount in the smallest currency unit (e.g., cents for IDR)
     * @param  string  $bank  Bank code for the transfer (default: 'bca').
     *                        Supported banks: bca, bni, bri, cimb
     * @return array Returns an associative array containing:
     *               - successful (bool): Whether the API request was successful
     *               - transaction_id (string|null): Midtrans transaction identifier
     *               - account (string|null): Virtual account number for the transfer
     *               - code (string|null): Biller code if applicable
     *               - message (string): Success message or error description
     *
     * @throws \Exception When an invalid bank code is provided
     */
    public function createBankTransfer(
        string $orderId,
        int $amount,
        string $bank = 'bca',
    ): array {
        // Validate bank
        $validBanks = [
            'bca',
            'bni',
            'bri',
            'cimb',
        ];
        if (! in_array($bank, $validBanks)) {
            throw new \Exception('Invalid bank. Valid banks are: '.implode(', ', $validBanks));
        }

        // Param
        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'bank_transfer' => [
                'bank' => $bank,
            ],
        ];

        // Response
        $response = $this->charge($params);
        $responseJson = $response->json();

        return [
            'successful' => $response->successful() && filled($responseJson['transaction_id'] ?? null),
            'transaction_id' => $responseJson['transaction_id'] ?? null,
            'account' => $responseJson['va_numbers'][0]['va_number'] ?? null,
            'code' => $responseJson['biller_code'] ?? null,
            'redirect_url' => $responseJson['redirect_url'] ?? null,
            'message' => $responseJson['message'] ?? 'Failed to create bank transfer',
        ];
    }

    /**
     * Creates a QRIS (Quick Response Code Indonesian Standard) payment transaction through Midtrans API.
     *
     * This method generates a QRIS payment by sending a charge request to Midtrans,
     * which returns a QR code URL that customers can scan to complete payment.
     *
     * @param  string  $orderId  Unique identifier for the order/transaction
     * @param  int  $amount  Transaction amount in the smallest currency unit (e.g., cents for IDR)
     * @return array Returns an associative array containing:
     *               - successful (bool): Whether the API request was successful
     *               - transaction_id (string|null): Midtrans transaction identifier
     *               - account (string|null): URL to the QR code image or payment page
     *               - code (null): Always null for QRIS payments
     *               - message (string): Success message or error description
     */
    public function createQris(
        string $orderId,
        int $amount,
    ): array {
        // Param
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
        ];

        // Response
        $response = $this->charge($params);
        $responseJson = $response->json();

        return [
            'successful' => $response->successful() && filled($responseJson['transaction_id'] ?? null),
            'transaction_id' => $responseJson['transaction_id'] ?? null,
            'account' => collect($responseJson['actions'] ?? [])->firstWhere('name', 'generate-qr-code')['url']
                ?? (isset($responseJson['transaction_id']) ? $this->baseUrl.'/v2/qris/'.$responseJson['transaction_id'].'/qr-code' : null),
            'code' => null,
            'redirect_url' => $responseJson['redirect_url'] ?? null,
            'message' => $responseJson['message'] ?? 'Failed to create qris',
        ];
    }

    /**
     * Creates a card payment transaction through Midtrans API.
     *
     * This method processes a card payment by sending a charge request to Midtrans
     * with the specified order details and card token.
     *
     * @param  string  $orderId  Unique identifier for the order/transaction
     * @param  int  $amount  Transaction amount in the smallest currency unit (e.g., cents for IDR)
     * @param  string  $cardToken  Tokenized representation of the customer's card details
     * @return array Returns an associative array containing:
     *               - successful (bool): Whether the API request was successful
     *               - transaction_id (string|null): Midtrans transaction identifier
     *               - account (null): Always null for card payments
     *               - code (null): Always null for card payments
     *               - message (string): Success message or error description
     */
    public function createCardTransaction(
        string $orderId,
        int $amount,
        string $cardToken,
    ) {
        // Param
        $params = [
            'payment_type' => 'credit_card',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'credit_card' => [
                'token_id' => $cardToken,
                'authentication' => true,
            ],
        ];

        // Response
        $response = Http::connectTimeout(5)->timeout(15)->withBasicAuth($this->serverKey, '')->post($this->baseUrl.'/v2/charge', $params);
        $responseJson = $response->json();

        return [
            'successful' => $response->successful(),
            'transaction_id' => $responseJson['transaction_id'] ?? null,
            'account' => $cardToken,
            'code' => null,
            'redirect_url' => $responseJson['redirect_url'] ?? null,
            'message' => $responseJson['message'] ?? 'Failed to create card transaction',
        ];
    }

    public function validateSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $hashedKey = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey);

        return $this->serverKey !== '' && hash_equals($hashedKey, $signatureKey);
    }

    private function charge(array $params): Response
    {
        if ($this->serverKey === '') {
            throw new RuntimeException('Server key Midtrans belum diatur di CMS.');
        }
        $client = Http::connectTimeout(5)->timeout(15)->withBasicAuth($this->serverKey, '');
        $response = $client->post($this->baseUrl.'/v2/charge', $params);
        if ((string) $response->json('status_code') === '406') {
            $response = $client->get($this->baseUrl.'/v2/'.rawurlencode($params['transaction_details']['order_id']).'/status');
            if ($response->json('order_id') !== $params['transaction_details']['order_id']
                || (int) $response->json('gross_amount') !== $params['transaction_details']['gross_amount']) {
                throw new RuntimeException('Pembayaran Midtrans sebelumnya tidak sesuai.');
            }
        }

        return $response;
    }
}
