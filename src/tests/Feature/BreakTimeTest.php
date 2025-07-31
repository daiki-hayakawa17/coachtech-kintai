<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class BreakTimeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_break_in_button_works_correctly()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'status' => 'working',
        ]);

        $response = $this->get('/attendance');

        $response->assertSee('<button type="submit" name="action_type" value="break_in"', false);

        $postResponse = $this->post('/attendance', [
            'action_type' => 'break_in',
        ]);

        $postResponse->assertRedirect('/attendance');

        $followUpResponse = $this->get('/attendance');

        $followUpResponse->assertSee('休憩中');
    }

    public function test_user_can_take_multiple_breaks_in_a_day()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'status' => 'working',
        ]);

        $this->from('/attendance')->post('/attendance', [
            'action_type' => 'break_in',
        ]);

        $this->post('/attendance', [
            'action_type' => 'break_out',
        ]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩入');
    }

    public function test_break_out_button_works_correctly()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'status' => 'working',
        ]);

        $this->from('/attendance')->post('/attendance', [
            'action_type' => 'break_in',
        ]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');

        $postResponse = $this->post('/attendance', [
            'action_type' => 'break_out',
        ]);

        $followUpResponse = $this->get('/attendance');

        $followUpResponse->assertSee('出勤中');
    }

    public function test_user_can_take_multiple_break_outs_in_a_day()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 14,00),
            'status' => 'working',
        ]);

        $this->from('/attendance')->post('/attendance', [
            'action_type' => 'break_in',
        ]);

        $this->post('/attendance', [
            'action_type' => 'break_out',
        ]);

        $this->post('/attendance', [
            'action_type' => 'break_in',
        ]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');
    }

    public function test_break_time_is_displayed_on_attendance_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-07-19',
            'clock_in' => Carbon::create(2025, 7, 19, 12,00),
            'status' => 'working',
        ]);

        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,00));

        $this->post('/attendance', [
            'action_type' => 'break_in',
        ]);

        Carbon::setTestNow(Carbon::create(2025, 7, 19, 14,30));

        $this->post('/attendance', [
            'action_type' => 'break_out',
        ]);

        $response = $this->get('/attendance/list');

        $response->assertSee('00:30');
    }
}
