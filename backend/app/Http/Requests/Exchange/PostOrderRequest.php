<?php

namespace App\Http\Requests;

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
        ];
    }

    public function messages()
    {
        return [
            'currency.required' => 'The currency field is required.',
        ];
    }
}
