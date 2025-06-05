<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech Kintai</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <a class="header__title" href="/">
            <img src="{{asset('storage/images/logo.svg') }}" class="header__logo" alt="Coachtechのロゴ">
        </a>
        <nav class="header__nav">
            <ul class="header__nav--list">
                <li class="header__nav--item">
                    <a class="header__nav--link">勤怠</a>
                </li>
                <li class="header__nav--item">
                    <a class="header__nav--link">勤怠一覧</a>
                </li>
                <li class="header__nav--item">
                    <a class="header__nav--link">申請</a>
                </li>
                @if (Auth::check())
                    <li class="header__nav--item">
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header__nav--button" type="submit">ログアウト</button>
                        </form>
                    </li>
                @else
                    <li class="header__nav--item">
                        <form action="/login" method="get">
                            <button class="header__nav--button" type="submit">
                                ログイン
                            </button>
                        </form>
                    </li>
                @endif
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>