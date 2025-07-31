<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachtechFreeMarket</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gorditas:wght@400;700&family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Noto+Serif+JP:wght@900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/email.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__title">
            <img src="{{ asset('images/logo.svg') }}" class="header__logo">
        </div>
    </header> 
    
    <main>
        <div class="text">
            <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p>メール認証を完了してください</p>
        </div>
        <form  class="verify__button">
            <button class="verify__button--submit">認証はこちらから</button>
        </form>
        @if (session('status') == 'verification-link-sent')
            <p>新しい確認メールを送信しました。</p>
        @endif
        <form method="POST" action="{{ route('verification.send') }}" class="resend__button">
        @csrf
            <button type="submit" class="resend__button--submit">確認メールを再送信</button>
        </form>
    </main>
</body>
</html>