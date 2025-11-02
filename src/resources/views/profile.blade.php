@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    @php
        $tab = request('tab', 'sell');
        $items = $tab === 'buy' ? ($purchasedItems ?? collect()) : ($sellingItems ?? collect());
    @endphp

    <div class="profile-page">

        <div class="user-info">
            <div class="user-info__avatar">
                <img src="{{ $profile->avatar_path ? asset('storage/' . $profile->avatar_path) : asset('images/avatar-placeholder.png') }}" alt="" aria-hidden="true">
            </div>
            <div class="user-info__name">{{ $user->name }}</div>
            <a href="{{ route('mypage.profile.edit') }}" class="user-info__edit">プロフィールを編集</a>
        </div>

        <div class="profile-tabs">
            <a href="{{ route('mypage', ['tab' => 'sell']) }}" class="tab {{ $tab === 'sell' ? 'is-active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('mypage', ['tab' => 'buy']) }}" class="tab {{ $tab === 'buy' ? 'is-active' : '' }}">
                購入した商品
            </a>
        </div>

        <div class="products-grid">
            @forelse ($items as $item)
                <a href="{{ route('items.show', $item->id) }}" class="product-card">
                    <div class="product-image">
                        @php
                            $src = $item->cover_image ?? '';
                            $isUrl = $src && (str_starts_with($src, 'http://') || str_starts_with($src, 'https://'));
                        @endphp
                        @if ($src)
                            <img src="{{ $isUrl ? $src : asset('storage/' . $src) }}"
                                 alt="{{ e($item->title) }}"
                                 onerror="this.src='{{ asset('images/placeholder-290.png') }}';this.onerror=null;">
                        @else
                            <img src="{{ asset('images/placeholder-290.png') }}" alt="no image">
                        @endif
                    </div>
                    <p class="product-name">{{ e($item->title) }}</p>
                </a>
            @empty
                <div class="empty">
                    <p>{{ $tab === 'sell' ? 'まだ出品はありません。' : 'まだ購入履歴はありません。' }}</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection