<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\EmployeeResource\Pages;
use App\Filament\Hr\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Notifications\JobApplicationStatusNotification;
use App\Services\AiEvaluationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = null;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNotIn('status', ['pending', 'rejected']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('filament.fields.user_account'))
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, ?Employee $record) {
                            $query->where(function ($q) use ($record) {
                                $q->doesntHave('employee');
                                if ($record) {
                                    $q->orWhere('id', $record->user_id);
                                }
                            });
                        }
                    )
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->label(__('filament.fields.full_name'))->required(),
                        Forms\Components\TextInput::make('email')->label(__('filament.fields.email'))->email()->required()->unique('users', 'email'),
                        Forms\Components\TextInput::make('password')->label(__('filament.fields.password'))->password()->revealable()->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $role = \App\Models\Role::where('name', 'employee')->first();
                        $user = \App\Models\User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                            'role_id' => $role ? $role->id : null,
                        ]);
                        return $user->id;
                    }),
                Forms\Components\Select::make('department_id')
                    ->label(__('filament.fields.department'))
                    ->relationship('department', 'name')
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('job_title')
                    ->label(__('filament.fields.job_title'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('salary')
                    ->label(__('filament.fields.salary'))
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$'),
                Forms\Components\Select::make('status')
                    ->label(__('filament.fields.employee_status'))
                    ->options([
                        'active' => __('filament.status.active'),
                        'on_leave' => __('filament.status.on_leave'),
                        'terminated' => __('filament.status.terminated'),
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\DatePicker::make('hire_date')
                    ->label(__('filament.fields.hire_date')),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label(__('filament.columns.department'))
                    ->searchable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('job_title')
                    ->label(__('filament.columns.job_title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('salary')
                    ->label(__('filament.columns.salary'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('filament.status.under_review'),
                        'active' => __('filament.status.active'),
                        'on_leave' => __('filament.status.on_leave'),
                        'terminated' => __('filament.status.terminated'),
                        'rejected' => __('filament.status.rejected_application'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'on_leave' => 'info',
                        'terminated' => 'danger',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label(__('filament.columns.hire_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.filter_by_status'))
                    ->options([
                        'active' => __('filament.status.active'),
                        'on_leave' => __('filament.status.on_leave'),
                        'terminated' => __('filament.status.terminated'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('ai_evaluate')
                    ->label(__('filament.actions.ai_evaluate'))
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.actions.ai_evaluate_heading'))
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::SevenExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('إغلاق')
                    ->modalContent(function (Employee $record) {
                        $evaluation = app(\App\Services\AiEvaluationService::class)->evaluate($record);
                        return new \Illuminate\Support\HtmlString('<div class="prose max-w-none dark:prose-invert" style="max-height: 70vh; overflow-y: auto; padding: 1rem;">' . \Illuminate\Support\Str::markdown($evaluation) . '</div>');
                    })
                    ->visible(function (Employee $record) {
                        if ($record->status !== 'active') return false;
                        
                        $user = $record->user;
                        if (!$user) return true;
                        
                        // إخفاء الزر عن الموظفين الذين هم مدراء نظام (Super Admin)
                        if ($user->role && $user->role->name === 'super_admin') return false;
                        
                        // إخفاء الزر إذا كان الموظف هو نفسه المستخدم الحالي (HR نفسه)
                        if ($user->id === auth()->id()) return false;
                        
                        return true;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SkillsRelationManager::class,
            RelationManagers\CertificatesRelationManager::class,
            RelationManagers\PayrollsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.employee');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.employees');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.employee_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.employees');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'job_title'];
    }
}
