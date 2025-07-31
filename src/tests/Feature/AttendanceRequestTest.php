<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use App\Models\BreakTime;
use App\Models\BreakTimeRequest;

class AttendanceRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    
    public function test_detail_page_displays_logged_in_users_name()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
        ]);

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance/' . $attendance->id);

        $response->assertSee('testuser');
    }

    public function test_detail_page_displays_selected_date()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $attendance2 = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-20',
            'clock_in' => Carbon::create(2025, 8, 20, 14,00),
            'clock_out' => Carbon::create(2025, 8, 20, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance/' . $attendance->id);

        $response->assertSee('2025年');
        $response->assertSee('8月19日');
        $response->assertDontSee('8月20日');
    }

    public function test_detail_page_displays_correct_clock_in_and_clock_out_time()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance/' . $attendance->id);

        $response->assertSee('14:00');
        $response->assertSee('18:00');
    }

    public function test_detail_page_displays_correct_break_time()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_in' => Carbon::create(2025, 8, 19, 15,00),
            'break_out' => Carbon::create(2025, 8, 19, 15,30),
        ]);

        $response = $this->get('/attendance/' . $attendance->id);

        $response->assertSee('15:00');
        $response->assertSee('15:30');
    }

    public function test_clock_in_fails_without_clock_out_after()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'clock_in' => Carbon::create(2025, 8, 19, 19,00),
        ]);

        $response->assertRedirect('/attendance/' . $attendance->id);

        $response->assertSessionHasErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_break_in_fails_without_clock_out_after()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_in' => Carbon::create(2025, 8, 19, 15,00),
            'break_out' => Carbon::create(2025, 8, 19, 15,30),
        ]);

        $response = $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'break_in' => [Carbon::create(2025, 8,19, 19,00),],
        ]);

        $response->assertRedirect('/attendance/' . $attendance->id);

        $response->assertSessionHasErrors([
            'break_in.0' => '休憩時間が勤務時間外です',
        ]);
    }

    public function test_break_out_fails_without_clock_out_after()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_in' => Carbon::create(2025, 8, 19, 15,00),
            'break_out' => Carbon::create(2025, 8, 19, 15,30),
        ]);

        $response = $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'break_out' => [Carbon::create(2025, 8,19, 19,00),],
        ]);

        $response->assertRedirect('/attendance/' . $attendance->id);

        $response->assertSessionHasErrors([
            'break_out.0' => '休憩時間が勤務時間外です',
        ]);
    }

    public function test_request_fails_without_note()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'clock_in' => Carbon::create(2025, 8,19, 14,30),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
        ]);

        $response->assertRedirect('/attendance/' . $attendance->id);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }

    public function test_attendance_request_correctly()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'clock_in' => '14:30',
            'clock_out' => '18:00',
            'note' => '遅刻のため',
        ]);

        $this->assertDatabaseHas('attendance_correct_requests', [
            'attendance_id' => $attendance->id,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendanceRequest = AttendanceCorrectRequest::where('attendance_id', $attendance->id)->first();

        $response = $this->actingAs($admin)->get('/stamp_correction_request/list');

        $response->assertSee('2025/08/19');

        $secondResponse = $this->get('/stamp_correction_request/approved/' . $attendanceRequest->id);

        $secondResponse->assertSee('14:30');
    }

    public function test_attendance_request_list_displays_weiting()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'clock_in' => '14:30',
            'clock_out' => '18:00',
            'note' => '遅刻のため',
        ]);

        $response = $this->get('/stamp_correction_request/list?page=waiting');

        $response->assertSee('承認待ち');
        $response->assertSee('2025/08/19');
        $response->assertSee('遅刻のため');
    }

    public function test_attendance_request_list_displays_approved()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'clock_in' => '14:30',
            'clock_out' => '18:00',
            'note' => '遅刻のため',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendanceRequest = AttendanceCorrectRequest::where('attendance_id', $attendance->id)->first();

        $this->actingAs($admin)->from('/stamp_correction_request/approved/' . $attendanceRequest->id)->post('/stamp_correction_request/approved/' . $attendanceRequest->id);

        $response = $this->get('/stamp_correction_request/list?page=approved');

        $response->assertSee('2025/08/19');
        $response->assertSee('遅刻のため');
    }

    public function test_detail_button_redirects_to_detail_page()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'clock_in' => '14:30',
            'clock_out' => '18:00',
            'note' => '遅刻のため',
        ]);

        $response = $this->get('/stamp_correction_request/list');

        $response->assertSee('href="/attendance/' . $attendance->id, false);

        $detail = $this->get('/attendance/' . $attendance->id);

        $detail->assertStatus(200);
        $detail->assertSee('2025年');
        $detail->assertSee('8月19日');
        $detail->assertSee('14:00');
    }
}
