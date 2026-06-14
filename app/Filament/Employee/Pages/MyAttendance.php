<?php

namespace App\Filament\Employee\Pages;

use App\Models\Attendance;
use App\Models\Employee;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class MyAttendance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.pages.my-attendance';
    protected static ?string $navigationGroup = null;
    protected static ?string $slug = 'my-attendance';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.attendance');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.my_attendance');
    }

    public function getEmployee(): ?Employee
    {
        return Employee::where('user_id', Auth::id())->first();
    }

    public function checkIn(): void
    {
        $employee = $this->getEmployee();
        if (!$employee) return;

        $today = now()->toDateString();
        $existing = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            Notification::make()->title(__('filament.attendance.already_checked_in'))->warning()->send();
            return;
        }

        $now = now();
        $status = $now->format('H:i:s') > '09:15:00' ? 'late' : 'present';

        if ($existing) {
            $existing->update(['check_in' => $now, 'status' => $status]);
        } else {
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in' => $now,
                'status' => $status,
            ]);
        }

        Notification::make()->title(__('filament.attendance.check_in_success'))->success()->send();
    }

    public function checkOut(): void
    {
        $employee = $this->getEmployee();
        if (!$employee) return;

        $today = now()->toDateString();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            Notification::make()->title(__('filament.attendance.must_check_in_first'))->warning()->send();
            return;
        }

        if ($attendance->check_out) {
            Notification::make()->title(__('filament.attendance.already_checked_out'))->warning()->send();
            return;
        }

        $now = now();
        $hoursWorked = Attendance::calculateHoursWorked($attendance->check_in, $now);

        $attendance->update([
            'check_out' => $now,
            'hours_worked' => $hoursWorked,
        ]);

        Notification::make()->title(__('filament.attendance.check_out_success'))->success()->send();
    }

    public function getMonthlyRecords()
    {
        $employee = $this->getEmployee();
        if (!$employee) return collect();

        return Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getViewData(): array
    {
        $employee = $this->getEmployee();
        $today = now()->toDateString();

        $todayRecord = $employee
            ? Attendance::where('employee_id', $employee->id)->where('date', $today)->first()
            : null;

        return [
            'todayRecord' => $todayRecord,
            'monthlyRecords' => $this->getMonthlyRecords(),
        ];
    }
}
