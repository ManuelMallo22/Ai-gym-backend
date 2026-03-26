<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'msisdn' => 'required|string|min:10|max:15',
        ];
    }

    public function messages()
    {
        return [
            'msisdn.required' => 'MSISDN is required',
            'msisdn.min' => 'MSISDN must be at least 10 characters',
            'msisdn.max' => 'MSISDN must not exceed 15 characters',
        ];
    }
}
