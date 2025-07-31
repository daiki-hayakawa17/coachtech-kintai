<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class StaffListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_can_confirmation_user_name_and_email()
    {
        $user = User::factory()->create([
            'name' => 'testuser1',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $secondUser = User::factory()->create([
            'name' => 'testuser2',
            'email' => 'test2@example.com',
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('admin/staff/list');

        $response->assertSee('testuser1');
        $response->assertSee('test@example.com');
        $response->assertSee('testuser2');
        $response->assertSee('test2@example.com');
    }

    public function test_staff_attendance_displayed_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 20,00));

        $user = User::factory()->create([
            'name' => 'testuser1',
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'clock_out' => Carbon::create(2025, 7, 19, 18,00),
            'status' => 'done',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/staff/' . $user->id);

        $response->assertSee('testuser1さんの勤怠');
        $response->assertSeeInOrder([
                '07/19(土)',
                '14:00',
                '18:00',
        ]);
    }

    public function test_previous_month_is_displayed_when_opening_staff_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create([
            'name' => 'testuser1',
            'role' => 'user',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-06-19',
            'clock_in' => Carbon::create(2025, 6, 19, 14,00),
            'clock_out' => Carbon::create(2025, 6, 19, 18,00),
            'status' => 'done',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/staff/' . $user->id);

        $response->assertSee('testuser1さんの勤怠');
        $response->assertSee('前月');

        $previous = $this->get('/admin/attendance/staff/' . $user->id . '?year=2025-06&month=2025-06');

        $previous->assertSee('testuser1さんの勤怠');
        $previous->assertSee('2025/06');
        $previous->assertSeeInOrder([
            '06/19(木)',
            '14:00',
            '18:00',
        ]);
    }

    public function test_next_month_is_displayed_when_opening_staff_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create([
            'name' => 'testuser1',
            'role' => 'user',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/staff/' . $user->id);

        $response->assertSee('testuser1さんの勤怠');
        $response->assertSee('前月');

        $previous = $this->get('/admin/attendance/staff/' . $user->id . '?year=2025-08&month=2025-08');

        $previous->assertSee('testuser1さんの勤怠');
        $previous->assertSee('2025/08');
        $previous->assertSeeInOrder([
            '08/19(火)',
            '14:00',
            '18:00',
        ]);
    }

    public function test_attendance_detail_button_redirects_to_detail_page()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'clock_out' => Carbon::create(2025, 7, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance/list');

        $response->assertSee('詳細');

        $this->get('/attendance/' . $attendance->id)
            ->assertStatus(200)
            ->assertSee('勤怠詳細')
            ->assertSee('2025年')
            ->assertSee('7月19日'); 
    }
}
