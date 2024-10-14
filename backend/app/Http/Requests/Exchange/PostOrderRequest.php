<?php

namespace App\Http\Requests\Exchange;

use Illuminate\Foundation\Http\FormRequest;

class PostOrderRequest extends FormRequest
{
    public function authorize()
    {

        return true;
    }

    public function rules()
    {
        return [
            'currency'         => 'required|integer',
            'protocol'         => 'required|integer',
            'amount'           => 'required|numeric|min:0',
            'target_currency'  => 'required|integer',
            'target_protocol'  => 'required|integer',
            'wallet_address'   => 'required|string',
            'email'            => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [
            'currency.required' => 'The currency field is required.',
            'email.email'       => 'The email must be a valid email address.',
        ];
    }
}
