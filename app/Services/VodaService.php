<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VodaService
{
    public string $baseUrl;

    public string $token;

    public function __construct()
    {
        $this->baseUrl = config('voda.base_url');
        $this->token = config('voda.token');
    }

    public function sendMessage(string $phone, string $message, ?bool $linkPreview = false): bool
    {
        $sendMessage = Http::post($this->baseUrl.'wuz/message', [
            'token' => $this->token,
            'phone' => $phone,
            'type' => 'text',
            'message' => $message,
            'link_preview' => $linkPreview,
        ]);

        if (! $sendMessage->successful()) {
            Log::error('Failed to send message to '.$phone.': '.$sendMessage->body());
            throw new \Exception('Failed to send message: '.$sendMessage->body());
        }

        return $sendMessage->successful();
    }
}
