<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_admin_can_view_all_users_attendance()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 20,00));

        $user = User::factory()->create([
            'name' => 'testuser',
            'role' => 'user',
        ]);

        $secondUser = User::factory()->create([
            'name' => 'testuser2',
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'clock_out' => Carbon::create(2025, 7, 19, 18,00),
            'status' => 'done',
        ]);

        $secondAttendance = Attendance::factory()->create([
            'user_id' => $secondUser->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'clock_out' => Carbon::create(2025, 7, 19, 18,00),
            'status' => 'done',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/list');

        $response->assertSee('testuser');
        $response->assertSee('testuser2');
        $response->assertSee('14:00');
        $response->assertSee('18:00');
        $response->assertSee('4:00');
    }

    public function test_admin_list_page_view_current_day()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 20,00));

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/list');

        $response->assertSee('2025年7月19日の勤怠');
        $response->assertSee('2025/07/19');
    }

    public function test_previous_day_is_displayed_when_opening_admin_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 20,00));

        $user = User::factory()->create([
            'name' => 'testuser',
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-18',
            'clock_in' => Carbon::create(2025, 7, 18, 14,00),
            'clock_out' => Carbon::create(2025, 7, 18, 18,00),
            'status' => 'done',
        ]);

        $this->actingAs($admin);

        $response = $this->get('admin/list');

        $response->assertSee('2025年7月19日の勤怠');
        $response->assertSee('前日');

        $previous = $this->get('/admin/list?year=2025-07-18&month=2025-07-18&day=2025-07-18');

        $previous->assertSee('2025年7月18日の勤怠');
        $previous->assertSee('testuser');
        $previous->assertSee('14:00');
    }

    public function test_next_day_is_displayed_when_opening_admin_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 20,00));

        $user = User::factory()->create([
            'name' => 'testuser',
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-20',
            'clock_in' => Carbon::create(2025, 7, 20, 14,00),
            'clock_out' => Carbon::create(2025, 7, 20, 18,00),
            'status' => 'done',
        ]);

        $this->actingAs($admin);

        $response = $this->get('admin/list');

        $response->assertSee('2025年7月19日の勤怠');
        $response->assertSee('前日');

        $previous = $this->get('/admin/list?year=2025-07-20&month=2025-07-20&day=2025-07-20');

        $previous->assertSee('2025年7月20日の勤怠');
        $previous->assertSee('testuser');
        $previous->assertSee('14:00');
    }
}
