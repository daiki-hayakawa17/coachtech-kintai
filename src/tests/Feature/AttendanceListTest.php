<?php

namespace Tests\Feature;

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

    public function test_clock_in_time_is_displayed_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->from('/attendance')->post('/attendance', [
            'action_type' => 'clock_in',
            'date' => '2025-07-19',
        ]);

        $followUpResponse = $this->get('/attendance/list');

        $followUpResponse->assertSee('14:00');
    }

    public function test_clock_out_time_is_displayed_on_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post('attendance',[
            'action_type' => 'clock_in',
            'date' => '2025-07-19',
        ]);

        Carbon::setTestNow(Carbon::create(2025, 7 ,19, 18,00));

        $this->post('/attendance', [
            'action_type' => 'clock_out',
        ]);

        $response = $this->get('attendance/list');

        $response->assertSee('18:00');
    }

    public function test_all_attendance_records_are_displayed_in_attendance_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendances = collect();
        for ($i = 0; $i < 3; $i++) {
            $date = Carbon::create(2025, 7,19)->addDays($i);
            $attendances->push(Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'clock_in' => $date->copy()->setTime(9,0),
                'clock_out' => $date->copy()->setTime(18,0),
                'status' => 'done',
            ]));
        }

        $response = $this->get('/attendance/list');

        foreach ($attendances as $attendance) {
            $dateFormatted = Carbon::parse($attendance->date)->isoFormat('MM/DD(ddd)');
            $clockIn = Carbon::parse($attendance->clock_in)->format('H:i');
            $clockOut = Carbon::parse($attendance->clock_out)->format('H:i');

            $response->assertSee("{$dateFormatted}");

            $response->assertSee("{$clockIn}");

            $response->assertSee("{$clockOut}");
        }
    }

    public function test_current_month_is_displayed_when_opening_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee('2025/07');
    }

    public function test_previous_month_is_displayed_when_opening_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-06-19',
            'clock_in' => Carbon::create(2025, 6, 19, 14,00),
            'clock_out' => Carbon::create(2025, 6, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance/list?year=2025-06&month=2025-06');

        $response->assertSee('2025/06');
        $response->assertSee('14:00');
        $response->assertSee('18:00');
    }

    public function test_next_month_is_displayed_when_opening_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance/list?year=2025-08&month=2025-08');

        $response->assertSee('2025/08');
        $response->assertSee('14:00');
        $response->assertSee('18:00');
    }

    public function test_attendance_detail_button_redirects_to_detail_page()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-08-19',
            'clock_in' => Carbon::create(2025, 8, 19, 14,00),
            'clock_out' => Carbon::create(2025, 8, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance/list');

        $response->assertSee('詳細');

        $this->get('/attendance/' . $attendance->id)
            ->assertStatus(200)
            ->assertSee('勤怠詳細')
            ->assertSee('2025年')
            ->assertSee('8月19日'); 
    }
}
