<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'zip' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/', 'size:8'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'zip' => '郵便番号',
            'address' => '住所',
        ];
    }

    public function messages(): array
    {
        return [
            'zip.regex' => '郵便番号は「123-4567」の形式で入力してください',
        ];
    }
}
