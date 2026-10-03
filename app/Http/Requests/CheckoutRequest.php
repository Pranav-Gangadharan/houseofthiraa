<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Accept "+91 98765 43210", "098765-43210" and friends.
        $phone = preg_replace('/\D+/', '', (string) $this->input('phone'));
        $phone = preg_replace('/^(91|0)(?=\d{10}$)/', '', $phone);

        $this->merge(['phone' => $phone]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['required', 'email:rfc', 'max:120'],
            'address' => ['required', 'string', 'min:8', 'max:300'],
            'pincode' => ['required', 'regex:/^[1-9]\d{5}$/'],
            'city' => ['required', 'string', 'max:60'],
            'state' => ['required', Rule::in(config('shop.states'))],
            'agree' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a 10-digit mobile number.',
            'pincode.regex' => 'Enter a 6-digit pincode.',
            'agree.accepted' => 'Please confirm to place your order.',
        ];
    }
}
