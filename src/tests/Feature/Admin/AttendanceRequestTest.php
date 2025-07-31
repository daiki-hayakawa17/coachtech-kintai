<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    
    public function test_attendance_detail_page_displays_selected_attendance_data()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-20',
            'clock_in' => Carbon::create(2025, 7, 20, 14,00),
            'clock_out' => Carbon::create(2025, 7, 20, 18,00),
            'status' => 'done',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/attendance/' . $attendance->id);

        $response->assertSee('testuser');
        $response->assertSee('2025年');
        $response->assertSee('7月20日');
        $response->assertSee('14:00');
    }

    public function test_clock_in_fails_without_clock_out_after()
    {
         $user = User::factory()->create([
            'name' => 'testuser',
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-20',
            'clock_in' => Carbon::create(2025, 7, 20, 14,00),
            'clock_out' => Carbon::create(2025, 7, 20, 18,00),
            'status' => 'done',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'clock_in' => '19:30',
            'clock_out' => '18:00',
            'note' => '遅刻のため',
        ]);

        $response->assertRedirect('/attendance/' . $attendance->id);

        $response->assertSessionHasErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_break_in_fails_without_clock_out_after()
    {
        $user = User::factory()->create();

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

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

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

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $this->actingAs($admin);

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

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $this->actingAs($admin);

        $response = $this->from('/attendance/' . $attendance->id)->post('/attendance/' . $attendance->id, [
            'clock_in' => Carbon::create(2025, 8,19, 14,30),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
        ]);

        $response->assertRedirect('/attendance/' . $attendance->id);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }
}
