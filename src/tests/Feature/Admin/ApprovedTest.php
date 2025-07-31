<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;

class ApprovedTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    
    public function test_attendance_request_list_displays_weiting()
    {
        Carbon::setTestNow(Carbon::create(2025, 8, 20, 14,00));

        $user = User::factory()->create([
            'name' => 'testuser1',
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $attendanceRequest = AttendanceCorrectRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'clock_in' => Carbon::create(2025, 8, 19, 14,30),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'waiting',
            'note' => '遅刻のため',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/stamp_correction_request/list?page=waiting');

        $response->assertSee('承認待ち');
        $response->assertSee('testuser1');
        $response->assertSee('2025/08/19');
        $response->assertSee('遅刻のため');
        $response->assertSee('2025/08/20');
    }

    public function test_attendance_request_list_displays_approved()
    {
        Carbon::setTestNow(Carbon::create(2025, 8, 20, 14,00));

        $user = User::factory()->create([
            'name' => 'testuser1',
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $attendanceRequest = AttendanceCorrectRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'clock_in' => Carbon::create(2025, 8, 19, 14,30),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'approved',
            'note' => '遅刻のため',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/stamp_correction_request/list?page=approved');

        $response->assertSee('承認済み');
        $response->assertSee('testuser1');
        $response->assertSee('2025/08/19');
        $response->assertSee('遅刻のため');
        $response->assertSee('2025/08/20');
    }

    public function test_attendance_request_detail_displays_correctoly()
    {
        Carbon::setTestNow(Carbon::create(2025, 8, 20, 14,00));

        $user = User::factory()->create([
            'name' => 'testuser1',
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $attendanceRequest = AttendanceCorrectRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'clock_in' => Carbon::create(2025, 8, 19, 14,30),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'waiting',
            'note' => '遅刻のため',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/stamp_correction_request/list?page=waiting');

        $response->assertSee('詳細');

        $detail = $this->get('/stamp_correction_request/approved/' . $attendanceRequest->id);

        $detail->assertSee('testuser1');
        $detail->assertSeeInOrder([
            '2025年',
            '8月19日',
        ]);
        $detail->assertSeeInOrder([
            '14:30',
            '～',
            '18:00',
        ]);
        $detail->assertSee('遅刻のため');
        $detail->assertSee('承認');
    }

    public function test_attendance_request_approved_correctoly()
    {
        Carbon::setTestNow(Carbon::create(2025, 8, 20, 14,00));

        $user = User::factory()->create([
            'name' => 'testuser1',
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $attendanceRequest = AttendanceCorrectRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'clock_in' => Carbon::create(2025, 8, 19, 14,30),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'waiting',
            'note' => '遅刻のため',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $this->post('/stamp_correction_request/approved/' . $attendanceRequest->id);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'date' => '2025-08-19',
            'clock_in' => '2025-08-19 14:30:00',
            'clock_out' => '2025-08-19 18:00:00',
        ]);
    }
}
