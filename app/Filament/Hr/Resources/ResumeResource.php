<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\ResumeResource\Pages;
use App\Filament\Hr\Resources\ResumeResource\RelationManagers;
use App\Models\Resume;
use App\Services\ResumeAnalysisService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResumeResource extends Resource
{
    protected static ?string $model = Resume::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'السير الذاتية';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.resume_file'))
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(__('filament.fields.employee'))
                            ->options(function (?Resume $record) {
                                return \App\Models\Employee::with('user')
                                    ->whereDoesntHave('resume')
                                    ->when($record, function ($query) use ($record) {
                                        $query->orWhere('id', $record->employee_id);
                                    })
                                    ->get()
                                    ->pluck('user.name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->unique(ignoreRecord: true),
                        Forms\Components\FileUpload::make('file_path')
                            ->label(__('filament.fields.resume_file'))
                            ->directory('resumes')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->preserveFilenames(),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.resume_extracted_text'))
                    ->description(__('filament.sections.resume_extracted_text_desc'))
                    ->schema([
                        Forms\Components\Textarea::make('resume_text')
                            ->label(__('filament.fields.resume_text'))
                            ->rows(8)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->label(__('filament.fields.file'))
                    ->url(fn ($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn () => __('filament.actions.download_view')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('analyze_resume')
                    ->label(__('filament.actions.ai_analyze_resume'))
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->modalHeading(__('filament.actions.ai_analyze_resume_heading'))
                    ->modalDescription(__('filament.actions.ai_analyze_resume_desc'))
                    ->modalSubmitActionLabel(__('filament.actions.start_analysis'))
                    ->form([
                        Forms\Components\Textarea::make('job_keywords')
                            ->label(__('filament.fields.job_keywords'))
                            ->placeholder(__('filament.fields.job_keywords_placeholder'))
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (Resume $record, array $data) {
                        $employee = $record->employee;
                        $employee->load(['user', 'department', 'skills']);

                        $skillsList = $employee->skills->pluck('name')->implode(', ');

                        $resumeData = [
                            'employee_name' => $employee->user?->name ?? 'غير محدد',
                            'job_title' => $employee->job_title ?? 'غير محدد',
                            'department' => $employee->department?->name ?? 'غير محدد',
                            'salary' => $employee->salary ?? 'غير محدد',
                            'status' => $employee->status ?? 'غير محدد',
                            'skills' => $skillsList ?: 'لا توجد مهارات مسجلة',
                            'resume_text' => $record->resume_text ?? 'لا يوجد نص للسيرة الذاتية',
                        ];

                        $service = app(ResumeAnalysisService::class);
                        $result = $service->analyzeResume($resumeData, $data['job_keywords']);

                        if (!$result) {
                            Notification::make()
                                ->title(__('filament.notifications.ai_analysis_error'))
                                ->body(__('filament.notifications.ai_analysis_error_body'))
                                ->danger()
                                ->persistent()
                                ->send();
                            return;
                        }

                        $score = $result['score'] ?? 0;
                        $recommendation = $result['recommendation'] ?? 'غير محدد';
                        $color = $score >= 70 ? 'success' : ($score >= 40 ? 'warning' : 'danger');

                        $reportLines = [
                            "**" . __('filament.fields.ai_score') . ": {$score}/100**",
                            "",
                            "**" . __('filament.fields.ai_recommendation') . ": {$recommendation}**",
                            "",
                            "**" . __('filament.fields.ai_summary') . ":**",
                            $result['summary'] ?? '',
                            "",
                            "**" . __('filament.fields.ai_report') . ":**",
                            $result['report'] ?? '',
                        ];

                        if (!empty($result['strengths'])) {
                            $reportLines[] = "";
                            $reportLines[] = "**" . __('filament.fields.ai_strengths') . ":**";
                            foreach ($result['strengths'] as $s) {
                                $reportLines[] = "- {$s}";
                            }
                        }

                        if (!empty($result['weaknesses'])) {
                            $reportLines[] = "";
                            $reportLines[] = "**" . __('filament.fields.ai_weaknesses') . ":**";
                            foreach ($result['weaknesses'] as $w) {
                                $reportLines[] = "- {$w}";
                            }
                        }

                        Notification::make()
                            ->title(__('filament.notifications.ai_analysis_complete') . " ({$score}/100)")
                            ->body(implode("\n", $reportLines))
                            ->{$color}()
                            ->persistent()
                            ->send();
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResumes::route('/'),
            'create' => Pages\CreateResume::route('/create'),
            'edit' => Pages\EditResume::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.resume');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.resumes');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.resumes');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.user.name'];
    }
}
