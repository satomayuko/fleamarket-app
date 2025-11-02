@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}?v=1">
@endsection

@section('content')
    <div class="items-top">
        @php
            $active = $active ?? request('tab', 'recommend');
        @endphp

        <div class="items-tabs">
            <a
                href="{{ route('items.index') }}"
                class="items-tabs__link {{ $active === 'recommend' ? 'is-active' : '' }}"
                @if ($active === 'recommend') aria-current="page" @endif
            >
                おすすめ
            </a>
            <a
                href="{{ route('items.index', ['tab' => 'mylist']) }}"
                class="items-tabs__link {{ $active === 'mylist' ? 'is-active' : '' }}"
                @if ($active === 'mylist') aria-current="page" @endif
            >
                マイリスト
            </a>
        </div>

        <div class="items-tabs__line"></div>

        @if ($active === 'mylist')
            @guest
                <div class="items-empty">
                    <p>マイリストを表示するにはログインしてください。</p>
                    <a class="items-empty__btn" href="{{ route('login') }}">ログインへ</a>
                </div>
            @else
                @if ($items->isEmpty())
                    <div class="items-empty">
                        <p>表示できる商品がありません。</p>
                    </div>
                @else
                    <div class="items-grid">
                        @foreach ($items as $item)
                            <a class="item-card" href="{{ route('items.show', $item) }}">
                                <div class="item-card__thumb">
                                    <img
                                        src="{{ $item->image_url }}"
                                        alt="{{ $item->name }}"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                    @if ($item->isSold())
                                        <span class="item-card__badge">Sold</span>
                                    @endif
                                </div>
                                <p class="item-card__name">{{ $item->name }}</p>
                            </a>
                        @endforeach
                    </div>

                    @if ($items->hasPages())
                        <nav class="pagination" role="navigation" aria-label="ページネーション">
                            {{ $items->links('pagination::tailwind') }}
                        </nav>
                    @endif
                @endif
            @endguest
        @else
            @if ($items->isEmpty())
                <div class="items-empty">
                    <p>表示できる商品がありません。</p>
                </div>
            @else
                <div class="items-grid">
                    @foreach ($items as $item)
                        <a class="item-card" href="{{ route('items.show', $item) }}">
                            <div class="item-card__thumb">
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->name }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                                @if ($item->isSold())
                                    <span class="item-card__badge">Sold</span>
                                @endif
                            </div>
                            <p class="item-card__name">{{ $item->name }}</p>
                        </a>
                    @endforeach
                </div>

                @if ($items->hasPages())
                    <nav class="pagination" role="navigation" aria-label="ページネーション">
                        {{ $items->links('pagination::default') }}
                    </nav>
                @endif
            @endif
        @endif
    </div>
@endsection