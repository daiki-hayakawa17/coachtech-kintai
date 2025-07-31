<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_current_datetime_is_displayed()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,50));

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $response->assertSee('2025年7月19日 (土)');

        $response->assertSee('14:50');
    }

    public function test_work_status_none_is_displayed()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $response->assertSee('勤務外');
    }

    public function test_work_status_working_is_displayed()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'status' => 'working',
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $response->assertSee('出勤中');
    }

    public function test_work_status_breaking_is_displayed()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'status' => 'breaking',
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $response->assertSee('休憩中');
    }

    public function test_work_status_done_is_displayed()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'clock_out' => Carbon::create(2025, 7, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $response->assertSee('退勤済');
    }

    public function test_clock_in_button_works_correctly()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('<button type="submit" name="action_type" value="clock_in"', false);

        $postResponse = $this->post('/attendance', [
            'action_type' => 'clock_in',
            'date' => '2025-07-19',
        ]);

        $postResponse->assertRedirect('/attendance');

        $followUpResponse = $this->get('/attendance');

        $followUpResponse->assertSee('出勤中');
    }

    public function test_user_cannot_clock_in_twice_on_same_day()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'clock_out' => Carbon::create(2025, 7, 19, 18,00),
            'status' => 'done',
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $response->assertDontSee('<button type="submit" name="action_type" value="clock_in"', false);
    }

    public function test_clock_out_button_works_correctly()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'clock_out' => null,
            'status' => 'working',
        ]);

        $response = $this->get('/attendance');

        $response->assertSee('<button type="submit" name="action_type" value="clock_out"', false);

        Carbon::setTestNow(Carbon::create(2025, 7, 19, 18,00));

        $this->post('/attendance', [
            'action_type' => 'clock_out',
            'date' => '2025-07-19',
        ]);

        $this->assertDatabaseHas('attendances', [
            'status' => 'done',
        ]);
    }

    
}
