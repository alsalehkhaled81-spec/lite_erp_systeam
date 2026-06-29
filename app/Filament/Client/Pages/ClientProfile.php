<?php

namespace App\Filament\Client\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class ClientProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.client-profile';
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
        $client = auth()->user();

        $this->form->fill([
            'name' => $client->name,
            'email' => $client->email,
            'company_name' => $client->company_name,
            'phone' => $client->phone,
            'address' => $client->address,
            'profile_photo_path' => $client->profile_photo_path,
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
                        TextInput::make('company_name')
                            ->label(__('filament.fields.company_name'))
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('filament.fields.phone'))
                            ->tel()
                            ->maxLength(20),
                        Textarea::make('address')
                            ->label(__('filament.fields.address'))
                            ->columnSpanFull(),
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
        $client = auth()->user();

        $client->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'company_name' => $data['company_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'profile_photo_path' => $data['profile_photo_path'] ?? $client->profile_photo_path,
        ]);

        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            if (Hash::check($data['current_password'], $client->password)) {
                $client->update(['password' => Hash::make($data['new_password'])]);
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
