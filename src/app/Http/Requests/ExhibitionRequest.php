<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'condition' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0', 'max:9999999'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '商品名',
            'description' => '商品説明',
            'image' => '商品画像',
            'categories' => '商品のカテゴリー',
            'condition' => '商品の状態',
            'price' => '商品価格',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名は必須です。',
            'name.max' => '商品名は255文字以内で入力してください。',
            'description.required' => '商品説明は必須です。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'image.required' => '商品画像は必須です。',
            'image.image' => '商品画像は画像ファイルを指定してください。',
            'image.mimes' => '商品画像は.jpegまたは.png形式でアップロードしてください。',
            'categories.required' => 'カテゴリーは少なくとも1つ選択してください。',
            'categories.array' => 'カテゴリーの形式が不正です。',
            'categories.min' => 'カテゴリーは少なくとも1つ選択してください。',
            'categories.*.exists' => '選択されたカテゴリーが存在しません。',
            'condition.required' => '商品の状態は必須です。',
            'price.required' => '価格は必須です。',
            'price.integer' => '価格は整数で入力してください。',
            'price.min' => '価格は0円以上で入力してください。',
            'price.max' => '価格は9999999円以内で入力してください。',
        ];
    }
}