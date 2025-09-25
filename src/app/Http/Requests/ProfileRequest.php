<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // プロフィール画像：.jpeg または .png
            'avatar'  => ['nullable', 'image', 'mimes:jpeg,png', 'max:5120'],

            // ユーザー名：必須・20文字以内
            'name'    => ['required', 'string', 'max:20'],

            // 郵便番号：必須・ハイフンあり・ちょうど8文字（例: 123-4567）
            'postal'  => ['required', 'regex:/^\d{3}-\d{4}$/', 'size:8'],

            // 住所：必須
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image'    => 'プロフィール画像は画像ファイルを指定してください。',
            'avatar.mimes'    => 'プロフィール画像は.jpegまたは.png形式でアップロードしてください。',
            'avatar.max'      => 'プロフィール画像は5MB以下にしてください。',

            'name.required'   => 'ユーザー名は必須です。',
            'name.max'        => 'ユーザー名は20文字以内で入力してください。',

            'postal.required' => '郵便番号は必須です。',
            'postal.regex'    => '郵便番号は「123-4567」の形式で入力してください。',
            'postal.size'     => '郵便番号はハイフンを含めて8文字で入力してください。',

            'address.required'=> '住所は必須です。',
            'address.max'     => '住所は255文字以内で入力してください。',
        ];
    }
}