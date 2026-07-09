<?php

namespace App\Filament\Hr\Resources\AttendanceResource\Pages;

use App\Filament\Hr\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DailyAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.daily-attendance';

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('filament.attendance.daily_attendance');
    }

    public function mount(): void
    {
        $this->form->fill([
            'date' => now()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament.attendance.daily_attendance_desc'))
                    ->schema([
                        DatePicker::make('date')
                            ->label(__('filament.attendance.choose_date'))
                            ->default(now())
                            ->maxDate(now())
                            ->live()
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getSelectedDate(): string
    {
        return $this->data['date'] ?? now()->toDateString();
    }

    protected function getRows(): array
    {
        $date = $this->getSelectedDate();

        $employees = Employee::with(['user', 'department'])
            ->where('status', 'active')
            ->whereHas('user', fn (Builder $q) => $q->where('is_approved', true))
            ->get()
            ->sortBy(fn (Employee $e) => $e->user?->name ?? '')
            ->values();

        $attendances = Attendance::whereDate('date', $date)
            ->get()
            ->keyBy('employee_id');

        $onDutyStatuses = ['present', 'late', 'half_day', 'over_time'];

        $rows = [];
        $presentCount = 0;

        foreach ($employees as $employee) {
            $record = $attendances->get($employee->id);
            $status = $record?->status;

            if (in_array($status, $onDutyStatuses)) {
                $presentCount++;
            }

            $rows[] = [
                'employee' => $employee,
                'attendance' => $record,
                'status' => $status,
                'on_duty' => in_array($status, $onDutyStatuses),
            ];
        }

        return [
            'rows' => $rows,
            'present_count' => $presentCount,
            'absent_count' => count($rows) - $presentCount,
            'total' => count($rows),
        ];
    }

    public function getViewData(): array
    {
        $result = $this->getRows();

        return [
            'rows' => $result['rows'],
            'totalEmployees' => $result['total'],
            'presentCount' => $result['present_count'],
            'absentCount' => $result['absent_count'],
            'date' => $this->getSelectedDate(),
            'dateLabel' => Carbon::parse($this->getSelectedDate())->translatedFormat('l d F Y'),
        ];
    }
}
