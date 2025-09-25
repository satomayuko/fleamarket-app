{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}?v=999">
  @yield('css')
  <title>@yield('title', config('app.name'))</title>
</head>
<body>
@php
  // 認証前専用ヘッダーを出すページ（ログイン/会員登録/メール認証誘導）
  $isAuthOnlyHeader = request()->routeIs('register','login','verification.notice');
@endphp

<header>
  @if ($isAuthOnlyHeader)
    {{-- ▼ 認証前（黒バー＋ロゴのみ） --}}
    <div class="auth-header">
      <div class="auth-header__inner">
        <a class="auth-header__logo" href="{{ url('/') }}">
          {{-- ログイン/登録画面は auth-header.png を使う --}}
          <img src="{{ asset('images/toppage-header-icon.png') }}" alt="COACHTECH">
        </a>
      </div>
    </div>
  @else
    {{-- ▼ 認証後（黒バー＋検索＋ナビ） --}}
    <div class="top-header">
      <div class="top-header__inner">
        {{-- 左：ロゴ --}}
        <a class="top-header__logo" href="{{ route('items.index') }}">
          {{-- トップページやマイページは toppage-header-icon.png を使う --}}
          <img src="{{ asset('images/toppage-header-icon.png') }}" alt="COACHTECH">
        </a>

        {{-- 中央：検索 --}}
        <form class="top-header__search" action="{{ route('items.index') }}" method="GET">
          <input
            type="text"
            name="keyword"
            placeholder="なにをお探しですか？"
            value="{{ request('keyword') }}"
          >
        </form>

        {{-- 右：ナビ --}}
        <nav class="top-header__nav">
          @auth
            <form method="POST" action="{{ route('logout') }}" class="top-header__logout">
              @csrf
              <button type="submit" class="top-header__link">ログアウト</button>
            </form>
            <a href="{{ route('mypage') }}" class="top-header__link">マイページ</a>
            <a href="{{ route('items.create') }}" class="top-header__cta">出品</a>
          @endauth

          @guest
            <a href="{{ route('login') }}" class="top-header__link">ログイン</a>
            <a href="{{ route('login') }}" class="top-header__link">マイページ</a>
            <a href="{{ route('login') }}" class="top-header__cta">出品</a>
          @endguest
        </nav>
      </div>
    </div>
  @endif
</header>

<main>
  @yield('content')
</main>

@yield('scripts')
</body>
</html>