<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $date = $this->faker->dateTimeBetween('-30 days', 'now');

        $clockIn = Carbon::instance($date)->setTime(rand(9,10), rand(0,59));

        $clockOut = (clone $clockIn)->addHours(rand(7,9))->addMinutes(rand(0,59));

        

        return [
            'user_id' => 1,
            'date' => $clockIn->toDatestring(),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'status' => $this->faker->randomElement(['working', 'break', 'done']),
        ];
    }
}
