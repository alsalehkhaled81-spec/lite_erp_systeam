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

        $hoursWorked = Attendance::calculateHoursWorked($checkInDt, $checkOutDt);
        $overtimeHours = max(0, $hoursWorked - 8);

        $status = 'present';
        $checkInTime = $checkIn->format('H:i');
        $checkOutTime = $checkOut->format('H:i');

        if ($hoursWorked < 4) {
            $status = 'absent';
        } elseif ($hoursWorked < 7) {
            $status = 'half_day';
        } elseif ($checkInTime < '09:00' || ($checkOutTime >= '17:00' && $hoursWorked > 8)) {
            $status = 'over_time';
        } elseif ($checkInTime > '09:15') {
            $status = 'late';
        }

        return [
            'employee_id' => Employee::factory(),
            'date' => $date,
            'check_in' => $checkInDt,
            'check_out' => $checkOutDt,
            'hours_worked' => $hoursWorked,
            'overtime_hours' => $overtimeHours,
            'status' => $status,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
