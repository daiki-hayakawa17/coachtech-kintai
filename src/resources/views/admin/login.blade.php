@extends('layouts.auth.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
    <form class="login__form" action="/admin/login" method="post">
        @csrf
        <h2 class="form__title">管理者ログイン</h2>
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
        <div class="form__button">
            <button class="form__button--submit" type="submit">管理者ログインする</button>
        </div>
    </form>  
@endsection