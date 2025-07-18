@extends($layout)

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">   
@endsection

@section('content')
    @php
        $break1 = $attendance->breaktimes->get(0);
        $break2 = $attendance->breaktimes->get(1);
    @endphp
    <form class="detail__content" action="/attendance/{{ $attendance->id }}" method="post">
        @csrf
        <h1 class="detail__title">勤怠詳細</h1>
        <div class="edit__form">
            <div class="user__name">
                <label>名前</label>
                <p class="user__name--inner">
                  {{ $attendance_user->name}}
                </p> 
            </div>
            <div class="attendance__date">
                <label>日付</label>
                <p class="attendance__date--year">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</p>
                <p class="attendance__date--day">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日')}}</p>
            </div>
            <div class="form__group--work">
                <label>出勤・退勤</label>
                <input type="time" name="clock_in" class="clock__in--input" value="{{\Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}">
                <span>～</span>
                <input type="time" name="clock_out" class="clock__out--input" value="{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}">
                <div class="form__error">
                    @error('clock_in')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group--break">
                <label>休憩</label>
                <input type="time" class="break__in--input" name="break_in[]" value="{{ optional($break1)->break_in ? \Carbon\Carbon::parse($break1->break_in)->format('H:i') : '' }}">
                <span>～</span>
                <input type="time" class="break__out--input" name="break_out[]" value="{{ optional($break1)->break_out ? \Carbon\Carbon::parse($break1->break_out)->format('H:i') : '' }}">
                @if ($errors->has('break_in.0') || $errors->has('break_out.0'))
                <div class="form__error">
                    @error('break_in.0')
                    {{ $message }}
                    @enderror
                </div>
                @endif
            </div>
            <div class="form__group--break">
                <label>休憩２</label>
                <input type="time" name="break_in[]" class="break__in--input--second" value="{{ optional($break2)->break_in ? \Carbon\Carbon::parse($break2->break_in)->format('H:i') : '' }}">
                <span>～</span>
                <input type="time" class="break__out--input" name="break_out[]" value="{{ optional($break2)->break_out ? \Carbon\Carbon::parse($break2->break_out)->format('H:i') : ''}}">
                @if ($errors->has('break_in.1') || $errors->has('break_out.1'))
                <div class="form__error">
                    @error('break_in.1')
                    {{ $message }}
                    @enderror
                </div>
                @endif
            </div>
            <div class="form__group--text">
                <label>備考</label>
                <textarea name="note"></textarea>
                <div class="form__error">
                    @error('note')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        @if(!$attendanceRequest || $attendanceRequest->status === 'approved')
        <div class="form__button">
            <button class="form__button--submit">
                修正
            </button>
        </div>
        @else
            <p class="already__requested">* 承認待ちのため修正はできません。</p>
        @endif
    </form>
@endsection