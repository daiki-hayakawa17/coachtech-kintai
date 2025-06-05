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
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>