<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectTemplateResource\Pages;
use App\Filament\Resources\ProjectTemplateResource\RelationManagers\TaskTemplatesRelationManager;
use App\Models\ProjectTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ProjectTemplateResource extends Resource
{
    protected static ?string $model = ProjectTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = null;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('filament.sections.template_details'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('filament.fields.template_name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label(__('filament.fields.description'))
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('budget')
                        ->label(__('filament.fields.budget'))
                        ->numeric()
                        ->prefix('$'),
                    Forms\Components\TextInput::make('estimated_days')
                        ->label(__('filament.fields.estimated_days'))
                        ->numeric()
                        ->suffix(__('filament.fields.days_unit')),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.fields.template_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->label(__('filament.columns.budget'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_days')
                    ->label(__('filament.fields.estimated_days'))
                    ->suffix(' ' . __('filament.fields.days_unit')),
                Tables\Columns\TextColumn::make('task_templates_count')
                    ->label(__('filament.columns.tasks_count'))
                    ->counts('taskTemplates')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Action::make('create_project')
                    ->label(__('filament.actions.create_project_from_template'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.actions.create_project_from_template'))
                    ->modalDescription(__('filament.actions.create_project_from_template_desc'))
                    ->form([
                        Forms\Components\TextInput::make('project_name')
                            ->label(__('filament.fields.project_name'))
                            ->required(),
                        Forms\Components\Select::make('client_id')
                            ->label(__('filament.fields.client'))
                            ->options(fn () => \App\Models\Client::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->action(function (ProjectTemplate $record, array $data) {
                        $project = $record->createProject([
                            'name' => $data['project_name'],
                            'client_id' => $data['client_id'] ?? null,
                        ]);

                        Notification::make()
                            ->title(__('filament.notifications.project_created_from_template'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            TaskTemplatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectTemplates::route('/'),
            'create' => Pages\CreateProjectTemplate::route('/create'),
            'edit' => Pages\EditProjectTemplate::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.project_template');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.project_templates');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.projects_tasks');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.project_templates');
    }
}
