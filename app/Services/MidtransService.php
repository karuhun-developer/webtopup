<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    public string $baseUrl;

    public function __construct(
        public ?string $serverKey = null,
        public ?string $clientKey = null,
        public ?bool $isProduction = null,
    ) {
        $this->serverKey = $this->serverKey ?? config('midtrans.server_key');
        $this->clientKey = $this->clientKey ?? config('midtrans.client_key');
        $this->isProduction = $this->isProduction ?? config('midtrans.is_production', false);
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
    ) {
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
        $response = Http::withBasicAuth($this->serverKey.':', '')->post($this->baseUrl.'/v2/charge', $params);
        $responseJson = $response->json();

        return [
            'successful' => $response->successful(),
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
    ) {
        // Param
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
        ];

        // Response
        $response = Http::withBasicAuth($this->serverKey.':', '')->post($this->baseUrl.'/v2/charge', $params);
        $responseJson = $response->json();

        return [
            'successful' => $response->successful(),
            'transaction_id' => $responseJson['transaction_id'] ?? null,
            'account' => $responseJson['actions'][0]['url'] ?? null,
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
        $response = Http::withBasicAuth($this->serverKey.':', '')->post($this->baseUrl.'/v2/charge', $params);
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

        return $hashedKey === $signatureKey;
    }
}
