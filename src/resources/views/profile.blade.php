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
      <img src="{{ $profile?->avatar_path ? asset('storage/'.$profile->avatar_path) : asset('images/avatar-placeholder.png') }}" alt="avatar">
    </div>
    <div class="user-info__name">{{ $user->name }}</div>
    <a href="{{ route('mypage.profile.edit') }}" class="user-info__edit">プロフィールを編集</a>
  </div>

  <div class="profile-tabs">
    <a href="{{ route('mypage', ['tab'=>'sell']) }}" class="tab {{ $tab==='sell' ? 'is-active' : '' }}">出品した商品</a>
    <a href="{{ route('mypage', ['tab'=>'buy'])  }}" class="tab {{ $tab==='buy'  ? 'is-active' : '' }}">購入した商品</a>
  </div>

  <div class="products-grid">
    @forelse($items as $item)
      <a href="{{ route('items.show', $item) }}" class="product-card">
        <div class="product-image">
          @if(!empty($item->cover_image))
            <img src="{{ asset('storage/'.$item->cover_image) }}" alt="{{ $item->title }}">
          @else
            <span class="product-image__placeholder">商品画像</span>
          @endif
        </div>
        <p class="product-name">{{ $item->title }}</p>
      </a>
    @empty
      @for($i=0;$i<8;$i++)
        <div class="product-card">
          <div class="product-image">
            <span class="product-image__placeholder">商品画像</span>
          </div>
          <p class="product-name">商品名</p>
        </div>
      @endfor
    @endforelse
  </div>

</div>
@endsection