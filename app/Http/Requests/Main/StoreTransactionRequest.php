<?php

namespace App\Http\Requests\Main;

use App\Actions\Main\CheckGameAccountAction;
use App\Models\PPOB\PPOBProduct;
use App\Services\PaymentGatewayService;
use App\Services\UsernameService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'nullable|in:id,id+server',
            'account_id' => 'required|string|max:100',
            'server_id' => 'required_if:type,id+server|nullable|string|max:100',
            'product_id' => 'required|exists:p_p_o_b_products,id',
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'payment_type' => 'required|in:automatic,manual',
            'payment_method' => ['required', 'string', 'max:255', Rule::when(
                $this->input('payment_type') === 'automatic',
                fn () => Rule::in(array_column(app(PaymentGatewayService::class)->methods(), 'id')),
            )],
            'voucher_code' => 'nullable|string|exists:vouchers,code',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $product = PPOBProduct::find($this->product_id);

        $game = UsernameService::gameForBrand($product->brand->name);
        if (in_array($game, ['mobilelegend', 'freefire'], true) || filled($product->brand->settings['username_service'] ?? null)) {
            $resolve = app(CheckGameAccountAction::class)->handle($this->validated());

            if (! $resolve['status']) {
                throw ValidationException::withMessages([
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
