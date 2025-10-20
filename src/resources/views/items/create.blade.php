@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-create.css') }}">
@endsection

@section('content')
<div class="sell">
    <h1 class="sell__title">商品の出品</h1>

    <form class="sell__form" method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
        @csrf

        <section class="block">
            <h2 class="block__title">商品画像</h2>
            <div class="image-drop">
                <label class="image-drop__inner">
                    <input type="file" name="image" accept="image/*" class="sr-only">
                    <button type="button" class="image-drop__button" tabindex="-1">画像を選択する</button>
                </label>
            </div>
            @error('image') <p class="error">{{ $message }}</p> @enderror
        </section>

        <section class="block">
            <div class="block__title-row">
                <h2 class="block__title block__title--muted">商品の詳細</h2>
            </div>
            <hr class="block__divider">

            <div class="subblock">
    <h3 class="subblock__label">カテゴリー</h3>
    <div class="chips">
        @php
            $chips = ['ファッション','家電','インテリア','レディース','メンズ','コスメ','本','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー','おもちゃ','ベビー・キッズ'];
        @endphp

        @foreach ($chips as $i => $chip)
            @php
                $id = 'cat_' . ($i + 1);
                $checked = in_array(($i + 1), (array)old('categories', []));
            @endphp

            <input
                id="{{ $id }}"
                class="chip-input"
                type="checkbox"
                name="categories[]"
                value="{{ $i + 1 }}"
                {{ $checked ? 'checked' : '' }}
            >

            <label for="{{ $id }}" class="chip">
                <span>{{ $chip }}</span>
            </label>
        @endforeach
    </div>
    @error('categories') <p class="error">{{ $message }}</p> @enderror
</div>

            <div class="subblock">
                <h3 class="subblock__label">商品の状態</h3>
                <div class="select">
                    <select name="condition" id="condition">
                        <option value="" hidden>選択してください</option>
                        @foreach (['新品','未使用に近い','目立った傷や汚れなし','やや傷や汚れあり','傷や汚れあり'] as $c)
                            <option value="{{ $c }}" {{ old('condition')===$c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <span class="select__arrow" aria-hidden="true">▾</span>
                </div>
                @error('condition') <p class="error">{{ $message }}</p> @enderror
            </div>
        </section>

        <section class="block">
            <div class="block__title-row">
                <h2 class="block__title block__title--muted">商品名と説明</h2>
            </div>
            <hr class="block__divider">

            <div class="field">
                <label class="field__label">商品名</label>
                <input class="input" type="text" name="name" value="{{ old('name') }}">
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field__label">ブランド名</label>
                <input class="input" type="text" name="brand" value="{{ old('brand') }}">
            </div>

            <div class="field">
                <label class="field__label">商品の説明</label>
                <textarea class="textarea" name="description" rows="5">{{ old('description') }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>
        </section>

        <section class="block">
            <div class="block__title-row">
                <h2 class="block__title">販売価格</h2>
            </div>
            <div class="price">
                <span class="price__yen">¥</span>
                <input class="price__input" type="number" inputmode="numeric" name="price" min="0" step="1" value="{{ old('price') }}">
            </div>
            @error('price') <p class="error">{{ $message }}</p> @enderror
        </section>

        <div class="submit">
            <button type="submit" class="submit__btn">出品する</button>
        </div>
    </form>
</div>
@endsection