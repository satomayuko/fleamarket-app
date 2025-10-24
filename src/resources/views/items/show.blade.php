@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-show.css') }}">
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__image">
        <img
            src="{{ $item->image_url }}"
            alt="{{ e($item->name) }}"
            onerror="this.src='{{ asset('images/placeholder-290.png') }}';this.onerror=null;"
        >
    </div>

    <div class="item-detail__info">
        @if($item->status === 'sold')
        <span class="item-detail__sold-label">SOLD</span>
        @endif

        <h1 class="item-detail__title">{{ e($item->name) }}</h1>

        <p class="item-detail__brand">
            {{ $item->brand ? e($item->brand) : 'ブランド名' }}
        </p>

        <p class="item-detail__price">
            ¥{{ number_format((int)($item->price ?? 0)) }}<span>（税込）</span>
        </p>

        <div class="item-detail__icons">
            <div class="icon-group">
                @auth
                    @if(Auth::user()->favoriteItems->contains($item->id))
                        <form method="POST" action="{{ route('items.unlike', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-button" aria-label="いいね解除">★</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('items.like', $item) }}">
                            @csrf
                            <button type="submit" class="icon-button" aria-label="いいねする">☆</button>
                        </form>
                    @endif
                @else
                    <span class="icon-button" aria-hidden="true">☆</span>
                @endauth
            </div>
            <span class="count count--like">{{ $item->favorites_count ?? ($item->favoredByUsers->count() ?? 0) }}</span>

            <span class="icon icon--comment" aria-hidden="true">💬</span>
            <span class="count count--comment">{{ $item->comments_count ?? ($item->comments->count() ?? 0) }}</span>
        </div>

        <button
            class="item-detail__buy"
            type="button"
            onclick="location.href='{{ Auth::check() ? route('orders.confirm', $item) : route('login', ['redirect' => route('orders.confirm', $item)]) }}'"
        >
            購入手続きへ
        </button>

        <section class="item-detail__section">
            <h2 class="section__title">商品説明</h2>
            <div class="section__text">
                <div>カラー：{{ $item->color ? e($item->color) : 'グレー' }}</div>
                <div>{{ $item->condition ? e($item->condition) : '新品' }}</div>
                <p>{{ $item->description ? nl2br(e($item->description)) : '商品の状態は良好です。傷もありません。購入後、即発送いたします。' }}</p>
            </div>
        </section>

        <section class="item-detail__section">
            <h2 class="section__title">商品の情報</h2>
            <div class="info-row">
                <span class="info-label">カテゴリー</span>
                <div class="info-chips">
                    @php
                        $cats = $item->relationLoaded('categories') ? $item->categories : $item->categories()->get();
                    @endphp
                    @if($cats->isNotEmpty())
                        @foreach($cats as $cat)
                            <span class="info-chip">{{ e($cat->name) }}</span>
                        @endforeach
                    @else
                        <span class="info-chip">未設定</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">商品の状態</span>
                <span class="info-value">{{ $item->condition ? e($item->condition) : '良好' }}</span>
            </div>
        </section>

        <section class="item-detail__section">
            <h2 class="section__title">コメント({{ $item->comments_count ?? ($item->comments->count() ?? 0) }})</h2>
            @forelse(($item->comments ?? collect()) as $comment)
                <div class="comment">
                    <div class="comment__user">
                        <div class="comment__avatar" aria-hidden="true"></div>
                        <span class="comment__name">{{ e(optional($comment->user)->name ?? 'ゲスト') }}</span>
                    </div>
                    <p class="comment__text">{{ e($comment->body) }}</p>
                </div>
            @empty
                <div class="comment comment--empty">こちらにコメントが入ります。</div>
            @endforelse

            @auth
                <form class="comment-form" method="POST" action="{{ route('comments.store', $item) }}">
                    @csrf
                    <label for="comment" class="comment-label">商品へのコメント</label>
                    <textarea id="comment" name="comment" rows="4" placeholder="コメントを入力してください"></textarea>
                    <button type="submit" class="comment-submit">コメントを送信する</button>
                </form>
            @else
                <div class="comment-form comment-form--guest">
                    <p>コメントするには
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}">ログイン</a>
                        が必要です。
                    </p>
                </div>
            @endauth

            @error('comment')
                <p class="error-message" style="color:red;">{{ $message }}</p>
            @enderror
        </section>
    </div>
</div>
@endsection