<?php

namespace App\Filament\Employee\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class EmployeeProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.employee-profile';
    protected static ?string $navigationLabel = null;
    protected static ?string $title = null;
    protected static ?string $navigationGroup = null;

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament.widgets.my_profile');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament.widgets.my_profile');
    }

    public function getTitle(): string
    {
        return __('filament.widgets.my_profile');
    }

    public function mount(): void
    {
        $user = auth()->user();
        $employee = $user->employee;

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_path' => $user->profile_photo_path,
            'job_title' => $employee?->job_title,
            'department' => $employee?->department?->name,
            'salary' => $employee?->salary,
            'hire_date' => $employee?->hire_date?->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament.sections.personal_info'))
                    ->schema([
                        FileUpload::make('profile_photo_path')
                            ->label(__('filament.fields.profile_photo'))
                            ->image()
                            ->avatar()
                            ->directory('profile-photos')
                            ->imageEditor()
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label(__('filament.fields.full_name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(__('filament.fields.email'))
                            ->email()
                            ->required(),
                    ])->columns(2),
                Section::make(__('filament.sections.job_info'))
                    ->schema([
                        TextInput::make('job_title')
                            ->label(__('filament.fields.job_title'))
                            ->disabled(),
                        TextInput::make('department')
                            ->label(__('filament.fields.department'))
                            ->disabled(),
                        TextInput::make('salary')
                            ->label(__('filament.fields.salary'))
                            ->prefix('$')
                            ->disabled(),
                        TextInput::make('hire_date')
                            ->label(__('filament.fields.hire_date'))
                            ->disabled(),
                    ])->columns(2),
                Section::make(__('filament.sections.change_password'))
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('filament.fields.current_password'))
                            ->password()->revealable(),
                        TextInput::make('new_password')
                            ->label(__('filament.fields.new_password'))
                            ->password()->revealable()
                            ->minLength(8),
                        TextInput::make('new_password_confirmation')
                            ->label(__('filament.fields.confirm_new_password'))
                            ->password()->revealable()
                            ->same('new_password'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'profile_photo_path' => $data['profile_photo_path'] ?? $user->profile_photo_path,
        ]);

        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            if (Hash::check($data['current_password'], $user->password)) {
                $user->update(['password' => Hash::make($data['new_password'])]);
            } else {
                Notification::make()
                    ->title(__('filament.notifications.wrong_password'))
                    ->danger()
                    ->send();
                return;
            }
        }

        Notification::make()
            ->title(__('filament.notifications.profile_updated'))
            ->success()
            ->send();
    }
}
