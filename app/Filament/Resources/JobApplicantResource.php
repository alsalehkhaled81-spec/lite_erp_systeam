<?php

namespace App\Filament\Resources;

use App\Filament\Hr\Resources\EmployeeResource\RelationManagers\SkillsRelationManager;
use App\Filament\Resources\JobApplicantResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Services\AiEvaluationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\JobApplicationStatusNotification;

class JobApplicantResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = null;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('status', ['pending', 'rejected']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.employee_data'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('filament.fields.user_account'))
                            ->relationship('user', 'name')
                            ->required(),
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
                    ])->columns(2),
            ]);
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
                    ->label(__('filament.fields.department'))
                    ->searchable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('job_title')
                    ->label(__('filament.fields.job_title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('salary')
                    ->label(__('filament.fields.salary'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('filament.status.under_review'),
                        'rejected' => __('filament.status.rejected_application'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label(__('filament.fields.hire_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.employee_status'))
                    ->options([
                        'pending' => __('filament.status.under_review'),
                        'rejected' => __('filament.status.rejected_application'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('accept')
                    ->label(__('filament.actions.accept_employment'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (Employee $record) {
                        $record->update([
                            'status' => 'active',
                            'hire_date' => now(),
                        ]);

                        if ($record->user) {
                            $record->user->notify(new JobApplicationStatusNotification('active', $record->user->name));
                        }
                    })
                    ->visible(fn (Employee $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label(__('filament.actions.reject_request'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('filament.actions.rejection_reason') . ' (' . __('filament.actions.rejection_reason_desc') . ')')
                            ->required()
                    ])
                    ->action(function (Employee $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        if ($record->user) {
                            $record->user->notify(new JobApplicationStatusNotification('rejected', $record->user->name));
                        }
                    })
                    ->visible(fn (Employee $record) => $record->status === 'pending'),
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
            SkillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobApplicants::route('/'),
            'create' => Pages\CreateJobApplicant::route('/create'),
            'edit' => Pages\EditJobApplicant::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.job_applicant');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.job_applicants');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.employee_management');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.job_applicants');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'job_title'];
    }
}
