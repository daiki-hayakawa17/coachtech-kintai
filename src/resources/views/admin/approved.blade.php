@extends('layouts.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/approved.css') }}">   
@endsection

@section('content')
    @php
        $break1 = $attendance->breaktimes->get(0);
        $break2 = $attendance->breaktimes->get(1);
    @endphp
    <form class="detail__content" action="/stamp_correction_request/approved/{{ $attendanceRequest->attendance_id }}" method="post">
        @csrf
        <h1 class="detail__title">勤怠詳細</h1>
        <div class="edit__form">
            <div class="user__name">
                <label>名前</label>
                <p class="user__name--inner">
                  {{ $user->name}}
                </p> 
            </div>
            <div class="attendance__date">
                <label>日付</label>
                <p class="attendance__date--year">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</p>
                <p class="attendance__date--day">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日')}}</p>
            </div>
            <div class="form__group--work">
                <label>出勤・退勤</label>
                <p class="clock__in">{{\Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}</p>
                <span>～</span>
                <p class="clock__out">{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}</p>
            </div>
            <div class="form__group--break">
                <label>休憩</label>
                <p class="break__in">{{ optional($break1)->break_in ? \Carbon\Carbon::parse($break1->break_in)->format('H:i') : '' }}</p>
                <span>～</span>
                <p class="break__out">{{ optional($break1)->break_out ? \Carbon\Carbon::parse($break1->break_out)->format('H:i') : '' }}</p>
            </div>
            <div class="form__group--break">
                <label>休憩２</label>
                <p class="break__in--second">{{ optional($break2)->break_in ? \Carbon\Carbon::parse($break2->break_in)->format('H:i') : '' }}</p>
                <span>～</span>
                <p class="break__out--second">{{ optional($break2)->break_out ? \Carbon\Carbon::parse($break2->break_out)->format('H:i') : ''}}</p>
            </div>
            <div class="form__group--text">
                <label>備考</label>
                <p class="note">{{ $attendanceRequest->note }}</p>
            </div>
        </div>
        <div class="form__button">
            @if ($attendanceRequest->status === 'waiting')
            <button class="form__button--submit">
                承認
            </button>
            @else
            <p class="approved__text">承認済み</p>
            @endif
        </div>
    </form>
@endsection