@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}?v=1">
@endsection

@section('content')
<div class="items-top">

  {{-- タブ（おすすめ / マイリスト） --}}
  @php
    $active = $active ?? request('tab', 'recommend');
  @endphp

  <div class="items-tabs">
    <a href="{{ route('items.index', ['tab' => 'recommend']) }}"
       class="items-tabs__link {{ $active === 'recommend' ? 'is-active' : '' }}">
      おすすめ
    </a>
    <a href="{{ route('items.index', ['tab' => 'mylist']) }}"
       class="items-tabs__link {{ $active === 'mylist' ? 'is-active' : '' }}">
      マイリスト
    </a>
  </div>
  <div class="items-tabs__line"></div>

  {{-- コンテンツ --}}
  @if ($active === 'mylist' && auth()->guest())
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
              <img src="{{ $item->image_Url }}" alt="{{ $item->name }}">
              @if ($item->isSold())
                <span class="item-card__badge">Sold</span>
              @endif
            </div>
            <p class="item-card__name">{{ $item->name }}</p>
          </a>
        @endforeach
      </div>

      {{-- ページネーション --}}
      <div class="pagination">
        {{ $items->links('pagination::tailwind') }}
      </div>
    @endif
  @endif

</div>
@endsection