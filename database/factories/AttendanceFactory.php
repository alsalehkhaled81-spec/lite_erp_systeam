<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d');
        $checkIn = fake()->dateTimeBetween('08:00', '09:30');
        $checkOut = fake()->dateTimeBetween('16:00', '18:00');

        $checkInDt = \Carbon\Carbon::parse($date . ' ' . $checkIn->format('H:i:s'));
        $checkOutDt = \Carbon\Carbon::parse($date . ' ' . $checkOut->format('H:i:s'));

        return [
            'employee_id' => Employee::factory(),
            'date' => $date,
            'check_in' => $checkInDt,
            'check_out' => $checkOutDt,
            'hours_worked' => Attendance::calculateHoursWorked($checkInDt, $checkOutDt),
            'status' => fake()->randomElement(['present', 'late', 'absent', 'half_day']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
