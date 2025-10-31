<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:convenience,card'],
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_method' => '支払い方法',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in'       => '支払い方法の選択が正しくありません',
        ];
    }
}