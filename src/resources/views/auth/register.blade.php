@extends('layouts.auth.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<form class="register__form" action="/register" method="post">
    @csrf
    <h2 class="form__title">会員登録</h2>
    <div class="form__group">
        <span class="form__label">名前</span>
        <div class="form__input">
            <input type="text" name="name" value="{{ old('name') }}">
        </div>
        <div class="form__error">
            @error('name')
            {{ $message }}
            @enderror
        </div>
    </div>
    <div class="form__group">
        <span class="form__label">メールアドレス</span>
        <div class="form__input">
            <input type="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="form__error">
            @error('email')
            {{ $message }}
            @enderror
        </div>
    </div>
    <div class="form__group">
        <span class="form__label">パスワード</span>
        <div class="form__input">
            <input type="password" name="password">
        </div>
        <div class="form__error">
            @error('password')
            {{ $message }}
            @enderror
        </div>
    </div>
    <div class="form__group">
        <span class="form__label">パスワード確認</span>
        <div class="form__input">
            <input type="password" name="password_confirmation">
        </div>
        <div class="form__error">
            @error('password_confirmation')
            {{ $message }}
            @enderror
        </div>
    </div>
    <div class="form__button">
        <button class="form__button--submit" type="submit">登録する</button>
    </div>
    <a class="login__link" href="/login">ログインはこちら</a>
</form>   
@endsection