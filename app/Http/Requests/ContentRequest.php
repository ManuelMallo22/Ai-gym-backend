<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
{
    public function authorize()
    {
        // Allow all requests (you can add auth/role checks if needed)
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ];
    }
}