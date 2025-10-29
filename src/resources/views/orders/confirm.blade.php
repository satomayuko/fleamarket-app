@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/order-confirm.css') }}">
@endsection

@section('content')
    <div class="confirm">
        <div class="confirm__inner">
            <div class="confirm__left">
                <div class="product">
                    <div class="product__thumb">
                        <img src="{{ $item->image_url ?? asset('images/placeholder-290.png') }}" alt="商品画像">
                        <span class="product__thumb-label">商品画像</span>
                    </div>
                    <div class="product__meta">
                        <h1 class="product__name">{{ $item->name }}</h1>
                        <p class="product__price">¥ {{ number_format($item->price) }}</p>
                    </div>
                </div>

                <hr class="sep">

                <div class="block">
                    <h2 class="block__title">支払い方法</h2>
                    <div class="select">
                        <select name="payment_method" id="payment_method">
                            <option value="" hidden>選択してください</option>
                            <option value="convenience">コンビニ払い</option>
                            <option value="card">クレジットカード</option>
                        </select>
                        <span class="select__arrow">▾</span>
                    </div>
                </div>

                <hr class="sep sep--wide">

                <div class="shipping">
                    <div class="shipping__head">
                        <h2 class="block__title">配送先</h2>
                        <a href="{{ route('orders.address', $item) }}" class="shipping__edit">変更する</a>
                    </div>
                    <address class="shipping__addr">
                        〒 {{ $defaultAddress['zip'] ?? 'XXX-YYYY' }}<br>
                        {{ $defaultAddress['line'] ?? 'ここには住所と建物が入ります' }}
                    </address>
                </div>

                <hr class="sep sep--wide">
            </div>

            <div class="confirm__right">
                <div class="summary">
                    <div class="summary__row">
                        <div class="summary__label">商品代金</div>
                        <div class="summary__value">¥ {{ number_format($item->price) }}</div>
                    </div>
                    <div class="summary__row">
                        <div class="summary__label">支払い方法</div>
                        <div class="summary__value" id="summary_method">コンビニ払い</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('orders.checkout', $item) }}">
                    @csrf
                    <input type="hidden" name="payment_method" id="payment_method_hidden" value="convenience">
                    <div class="action">
                        <button type="submit" class="buy-btn">購入する</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const sel = document.getElementById('payment_method');
        const out = document.getElementById('summary_method');
        const hidden = document.getElementById('payment_method_hidden');

        const labels = {
            convenience: 'コンビニ払い',
            card: 'クレジットカード'
        };

        function reflect() {
            const val = sel.value || 'convenience';
            out.textContent = labels[val] || '選択してください';
            hidden.value = val;
        }

        reflect();
        sel.addEventListener('change', reflect);
    </script>
@endsection