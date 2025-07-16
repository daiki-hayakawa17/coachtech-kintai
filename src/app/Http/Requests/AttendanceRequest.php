<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in' => ['before:clock_out'],
            'clock_out' => ['after:clock_in'],
            'break_in.*' => ['nullable', 'before:clock_out', 'after:clock_in'],
            'break_out.*' => ['nullable', 'before:clock_in', 'after:clock_out'],
            'note' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'clock_in.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_in.*.before' => '休憩時間が勤務時間外です',
            'break_in.*.after' => '休憩時間が勤務時間外です',
            'break_out.*.before' => '休憩時間が勤務時間外です',
            'break_out.*.after' => '休憩時間が勤務時間外です',
            'note.required' => '備考を記入してください',
        ];
    }
}
