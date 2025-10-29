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
            $isAuthOnlyHeader = request()->routeIs('register', 'login', 'verification.notice');
        @endphp

        <header>
            @if ($isAuthOnlyHeader)
                <div class="auth-header">
                    <div class="auth-header__inner">
                        <a class="auth-header__logo" href="{{ url('/') }}">
                            <img src="{{ asset('images/toppage-header-icon.png') }}" alt="COACHTECH">
                        </a>
                    </div>
                </div>
            @else
                <div class="top-header">
                    <div class="top-header__inner">
                        <a class="top-header__logo" href="{{ route('items.index') }}">
                            <img src="{{ asset('images/toppage-header-icon.png') }}" alt="COACHTECH">
                        </a>

                        <form class="top-header__search" action="{{ route('items.index') }}" method="GET">
                            <input
                                type="text"
                                name="keyword"
                                placeholder="なにをお探しですか？"
                                value="{{ old('keyword', $keyword ?? request('keyword')) }}"
                            >
                        </form>

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