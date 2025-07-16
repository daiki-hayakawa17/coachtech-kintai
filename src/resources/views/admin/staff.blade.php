@extends('layouts.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}">   
@endsection

@section('content')
    <div class="staff__list">
        <h1 class="staff__list--title">スタッフ一覧</h1>
        <table class="staff__list--table">
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><a class="staff__attendance--link" href="/admin/attendance/staff/{{ $user->id }}">詳細</a></td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection