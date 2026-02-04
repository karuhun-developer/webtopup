<?php

namespace App\Http\Requests\Main;

use App\Models\PPOB\PPOBProduct;
use App\Services\GameService;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'nullable|in:id,id+server',
            'account_id' => 'required_if:type,id',
            'server_id' => 'required_if:type,id+server',
            'product_id' => 'required|exists:p_p_o_b_products,id',
            'email' => 'nullable|email|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|number|min:10|max:15',
            'payment_type' => 'required|in:automatic,manual',
            'payment_method' => 'required|in:qris,bca,bni,bri,mandiri,permata',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $product = PPOBProduct::find($this->product_id);

        // Game service
        $gameService = new GameService;

        // Check the brand if the brand is mobile legends, check the server and uid
        if (strtolower($product->brand->name) === 'mobile legends') {
            $isValid = $gameService->isIdValid(
                game: 'mobilelegends',
                server: $this->server_id,
                uid: $this->account_id,
            );

            if (! $isValid['status']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'account_id' => 'Game id or server is invalid',
                ]);
            }
        }

        // Merge additional data
        $this->merge([
            'p_p_o_b_brand_id' => $product->p_p_o_b_brand_id,
            'p_p_o_b_product_id' => $product->id,
            'submited' => [
                'account_id' => $this->account_id,
                'server_id' => $this->server_id ?? null,
            ],
        ]);
    }
}
