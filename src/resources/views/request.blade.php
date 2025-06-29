@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/request.css') }}">  
@endsection

@section('content')
    <div class="request__list">
        <h1 class="request__list--title">申請一覧</h1>
        <div class="request__list--inner">
            <div class="request__list--tub">
                <a class="list__tub--text {{ request('page', 'waiting') === 'waiting' ? 'active' : ''}}" href="{{ route('request.list', ['page' => 'waiting']) }}">
                    承認待ち
                </a>
                <a class="list__tub--text {{ request('page') === 'approved' ? 'active' : ''}} approved" href="{{ route('request.list', ['page' => 'approved']) }}">
                    承認済み
                </a>
            </div>
            <table class="request__list--table">
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
                @foreach ($attendanceRequests as $attendanceRequest)
                <tr>
                    @if ($attendanceRequest->status === 'waiting')
                        <td>承認待ち</td>
                    @elseif($attendanceRequest->status === 'approved')
                        <td>承認済み</td>
                    @endif
                    <td>{{ $user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendanceRequest->attendance->date)->format('Y/m/d') }}</td>
                    <td>{{ $attendanceRequest->note }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendanceRequest->created_at)->format('Y/m/d') }}</td>
                    <td><a href="/attendance/{{ $attendanceRequest->attendance_id }}" class="detail__link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection