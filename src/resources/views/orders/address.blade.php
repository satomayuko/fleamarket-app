@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase-address.css') }}">
@endsection

@section('content')
    <div class="address">
        <div class="address__inner">
            <h1 class="address__title">住所の変更</h1>

            <form method="POST" action="{{ route('orders.address.update', $item) }}">
                @csrf

                <div class="form__group">
                    <label for="zip" class="form__label">郵便番号</label>
                    <input
                        id="zip"
                        type="text"
                        name="zip"
                        class="form__input"
                        value="{{ old('zip', $address->zip ?? '') }}"
                        placeholder="123-4567"
                        inputmode="numeric"
                        maxlength="8"
                        required
                    >
                    @error('zip')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__group">
                    <label for="address" class="form__label">住所</label>
                    <input
                        id="address"
                        type="text"
                        name="address"
                        class="form__input"
                        value="{{ old('address', $address->address ?? '') }}"
                        required
                    >
                    @error('address')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__group">
                    <label for="building" class="form__label">建物名</label>
                    <input
                        id="building"
                        type="text"
                        name="building"
                        class="form__input"
                        value="{{ old('building', $address->building ?? '') }}"
                    >
                    @error('building')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form__action">
                    <button type="submit" class="btn btn--red">更新する</button>
                </div>
            </form>
        </div>
    </div>
@endsection