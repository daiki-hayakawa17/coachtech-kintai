@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}"
@endsection

@section('content')
    <form class="attendance__form" action="/attendance" method="post">
        @csrf
        <div class="attendance__status">
            <p class="attendance__status--inner">
                {{ $statusLabel }}
            </p>
        </div>

        <div class="attendance__date">
            <p class="attendance__date--inner">
                {{ $today }}
            </p>
            <input type="hidden" name="date" value="{{ $date }}">
        </div>

        <div class="attendance__datetime">
            <p class="attendance__datetime--inner">
                {{ $now }}
            </p>
            <input type="hidden" name="action_type" value="{{ $now }}">
        </div>

        <div class="attendance__button">
            @if ($statusLabel === '勤務外')
                <button type="submit" name="action_type" value="clock_in" class="clock__in--button">出勤</button>
            @elseif($statusLabel === '出勤中')
                <button type="submit" name="action_type" value="clock_out" class="clock__out--button">退勤</button>
                <button type="submit" name="action_type" value="break_in" class="break__in--button">休憩入</button>
            @elseif($statusLabel === '休憩中')
                <button type="submit" name="action_type" value="break_out" class="break__out--button">休憩戻</button>
            @else
                <p class="done__text">お疲れさまでした。</p>
            @endif
        </div>
    </form>
@endsection