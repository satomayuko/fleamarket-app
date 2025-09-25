@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')
<div class="items-index">
  <div class="items-tabs">
    <a href="{{ route('items.index') }}"
       class="items-tab {{ ($active ?? 'all') === 'all' ? 'is-active' : '' }}">
      おすすめ
    </a>

    @auth
      <a href="{{ route('items.index', ['tab' => 'mylist']) }}"
         class="items-tab {{ ($active ?? '') === 'mylist' ? 'is-active' : '' }}">
        マイリスト
      </a>
    @endauth
  </div>

  @if($items->count())
    <ul class="items-grid">
      @foreach($items as $item)
        <li class="items-card">
          <a href="{{ route('items.show', $item) }}" class="items-link">
            <div class="items-thumb">
              @if($item->cover_image)
                <img src="{{ asset('storage/'.$item->cover_image) }}" alt="{{ $item->title }}">
              @else
                <img src="{{ asset('images/noimage.png') }}" alt="no image">
              @endif
            </div>
            <div class="items-title">{{ $item->title }}</div>
          </a>
        </li>
      @endforeach
    </ul>

    <div class="items-pagination">
      {{ $items->links() }}
    </div>
  @else
    <p class="items-empty">表示できる商品がありません。</p>
  @endif

</div>
@endsection