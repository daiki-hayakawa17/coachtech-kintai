@extends('layouts.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff_attendance.css')}}">
@endsection

@section('content')
    @php
    $prevMonth = $carbonMonth->copy()->subMonth()->format('Y-m');
    $nextMonth = $carbonMonth->copy()->addMonth()->format('Y-m');
    @endphp
    <div class="staff__attendance--list">
        <h1 class="list__title">
            {{ $user->name }}さんの勤怠
        </h1>
        <div class="month__title">
            <a class="previous__month" href="{{ route('staff.attendance', ['user_id' => $user->id, 'year' => $prevMonth, 'month' => $prevMonth]) }}">
                <img class="left__arrow" src="{{ asset('images/leftArrow.svg') }}" alt="左矢印">
                <p class="previous__month--text">前月</p>
            </a>
            <div class="current__month">
                <img class="calendar__image" src="{{ asset('images/calendar.png') }}" alt="カレンダー">
                <h2 class="current__month--text">{{ $currentMonth }}</h2>
            </div>
            <a class="next__month" href="{{ route('staff.attendance', ['user_id' => $user->id, 'year' => $nextMonth, 'month' => $nextMonth]) }}">
                <p class="next__month--text">翌月</p>
                <img class="right__arrow" src="{{ asset('images/rightArrow.svg') }}" alt="右矢印">
            </a>
        </div>

        <table class="attendance__list">
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
            @foreach ($dates as $date)
            @php
                $attendance = $attendances[$date->format('Y-m-d')] ?? null;
            @endphp
            <tr>
                <td>{{ $date->isoFormat('M月D日(ddd)') }}</td>
                <td>{{ isset($attendance) && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                <td>{{ isset($attendance) && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                <td>{{ $attendance ?  $attendance->formatted_break_time : '-' }}</td>
                <td>{{ $attendance ? $attendance->formatted_work_time : '-' }}</td>
                <td>
                    @if ($attendance)
                        <a href="/attendance/{{ $attendance->id }}" class="detail__link">詳細</a>
                    @else
                        <p class="detail__link">詳細</p>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        <div class="csv__button">
            <a href="{{ route('admin.staff.export', ['user_id' => $user->id, 'month' => $carbonMonth->format('Y-m')]) }}" class="csv__button--submit">
                CSV出力
            </a>
        </div>
    </div>
@endsection