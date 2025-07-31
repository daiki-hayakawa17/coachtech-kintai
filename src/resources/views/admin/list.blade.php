@extends('layouts.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/list.css') }}">   
@endsection

@section('content')
    @php
        $prevDay = $carbonDay->copy()->subDay()->format('Y-m-d');
        $nextDay = $carbonDay->copy()->addDay()->format('Y-m-d');
    @endphp
    <div class="list__content">
        <h1 class="list__title">{{ $currentDay->isoFormat('YYYY年M月D日') }}の勤怠</h1>
        <div class="day__title">
            <a class="previous__day" href="{{ route('admin.list', ['year' => $prevDay, 'month' => $prevDay, 'day' => $prevDay]) }}">
                <img class="left__arrow" src="{{ asset('images/leftArrow.svg') }}" alt="左矢印">
                <p class="previous__day--text">前日</p>
            </a>
            <div class="current__day">
                <img class="calendar__image" src="{{ asset('images/calendar.png') }}" alt="カレンダー">
                <h2 class="current__day--text">{{ $currentDay->format('Y/m/d') }}</h2>
            </div>
            <a class="next__day" href="{{ route('admin.list', ['year' => $nextDay, 'month' => $nextDay, 'day' => $nextDay]) }}">
                <p class="next__day--text">翌日</p>
                <img class="right__arrow" src="{{ asset('images/rightArrow.svg') }}" alt="右矢印">
            </a>
        </div>

        <table class="attendance__list">
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
            @foreach ($users as $user)
            @php
                $attendance = $user->attendances->first();
            @endphp
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}</td>
                    <td>{{ $attendance->formatted_break_time }}</td>
                    <td>{{ $attendance->formatted_work_time }}</td>
                    <td><a href="/attendance/{{ $attendance->id }}" class="detail__link">詳細</a></td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection