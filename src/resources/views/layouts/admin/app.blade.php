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
        <a class="header__title" href="/attendance">
            <img src="{{asset('storage/images/logo.svg') }}" class="header__logo" alt="Coachtechのロゴ">
        </a>
        <nav class="header__nav">
            <ul class="header__nav--list">
                @if ($statusLabel !== '退勤済')
                    <li class="header__nav--item">
                        <a class="header__nav--link" href="/admin/list">勤怠一覧</a>
                    </li>
                    <li class="header__nav--item">
                        <a class="header__nav--link" href="/admin/staff/list">スタッフ一覧</a>
                    </li>
                    <li class="header__nav--item">
                        <a class="header__nav--link" href="{{ route('request.list') }}">申請一覧</a>
                    </li>
                @else
                    <li class="header__nav--item">
                        <a class="header__nav--link-done" href="/attendance/list">今月の出勤一覧</a>
                    </li>
                    <li class="header__nav--item">
                        <a class="header__nav--link-done">申請一覧</a>
                    </li>
                @endif
                <li class="header__nav--item">
                    <form action="/logout" method="post">
                        @csrf
                        <button class="header__nav--button" type="submit">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>