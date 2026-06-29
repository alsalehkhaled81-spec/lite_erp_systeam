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

    protected static ?string $navigationGroup = null;

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
                            ->disk('public')
                            ->directory('resumes')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->preserveFilenames()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if ($state) {
                                    $path = $state->getRealPath();
                                    $mime = $state->getMimeType();
                                    $parser = new \App\Services\ResumeParserService();
                                    $text = $parser->parse($path, $mime);
                                    if (empty(trim($text))) {
                                        $text = 'تعذر استخراج النص تلقائياً من الملف المرفق. قد يكون الملف عبارة عن صور ممسوحة ضوئياً (Scanned).';
                                    }
                                    $set('resume_text', $text);
                                }
                            }),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.resume_extracted_text'))
                    ->description(__('filament.sections.resume_extracted_text_desc'))
                    ->schema([
                        Forms\Components\Textarea::make('resume_text')
                            ->label(__('filament.fields.resume_text'))
                            ->rows(8)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('filament.sections.ai_analysis_results'))
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('ai_score')
                                ->label(__('filament.fields.ai_score'))
                                ->disabled(),
                            Forms\Components\TextInput::make('ai_recommendation')
                                ->label(__('filament.fields.ai_recommendation'))
                                ->disabled(),
                        ]),
                        Forms\Components\Textarea::make('ai_summary')
                            ->label(__('filament.fields.ai_summary'))
                            ->rows(2)
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('ai_report')
                            ->label(__('filament.fields.ai_report'))
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn (?Resume $record) => $record?->analyzed_at === null),
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
                Tables\Columns\TextColumn::make('employee.vacancy.title')
                    ->label(__('filament.fields.vacancy_title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employee.status')
                    ->label(__('filament.fields.application_status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __('filament.status.application_' . $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('ai_score')
                    ->label(__('filament.fields.ai_score'))
                    ->badge()
                    ->color(fn ($state): string => $state === null ? 'gray' : ($state >= 70 ? 'success' : ($state >= 40 ? 'warning' : 'danger')))
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->label(__('filament.fields.file'))
                    ->url(fn ($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn () => __('filament.actions.download_view')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.application_date'))
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
                Tables\Filters\SelectFilter::make('application_status')
                    ->label(__('filament.fields.application_status'))
                    ->options([
                        'pending' => __('filament.status.application_pending'),
                        'active' => __('filament.status.application_active'),
                        'rejected' => __('filament.status.application_rejected'),
                        'on_leave' => __('filament.status.application_on_leave'),
                        'terminated' => __('filament.status.application_terminated'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $q, $value) => $q->whereHas('employee', fn ($q2) => $q2->where('status', $value))
                    ))
                    ->default('pending'),
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
                    ->fillForm(fn (Resume $record): array => [
                        'target_job_title' => $record->employee?->vacancy?->title ?? $record->employee?->job_title ?? '',
                        'job_keywords' => $record->employee?->vacancy?->requirements ?? '',
                    ])
                    ->form([
                        \Filament\Forms\Components\TextInput::make('target_job_title')
                            ->label('المسمى الوظيفي المستهدف')
                            ->placeholder('مثال: مطور PHP')
                            ->required()
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Textarea::make('job_keywords')
                            ->label(__('filament.fields.job_keywords'))
                            ->placeholder(__('filament.fields.job_keywords_placeholder'))
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (Resume $record, array $data) {
                        $employee = $record->employee;
                        if ($employee) {
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
                        } else {
                            $resumeData = [
                                'resume_text' => $record->resume_text ?? 'لا يوجد نص للسيرة الذاتية',
                            ];
                        }

                        $service = app(\App\Services\ResumeAnalysisService::class);
                        $result = $service->analyzeResume($resumeData, $data['job_keywords'], $data['target_job_title']);

                        if (!$result) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament.notifications.ai_analysis_error'))
                                ->body(__('filament.notifications.ai_analysis_error_body'))
                                ->danger()
                                ->persistent()
                                ->send();
                            return;
                        }

                        $score = $result['score'] ?? 0;

                        $record->update([
                            'ai_score' => $result['score'] ?? null,
                            'ai_summary' => $result['summary'] ?? null,
                            'ai_report' => $result['report'] ?? null,
                            'ai_recommendation' => $result['recommendation'] ?? null,
                            'analyzed_at' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title(__('filament.notifications.ai_analysis_complete') . " ({$score}/100)")
                            ->body('تم التقييم والتخزين بنجاح. اضغط على "عرض التحليل" لمشاهدة التقرير المفصل.')
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\Action::make('view_analysis')
                    ->label('عرض التحليل')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->modalHeading('نتيجة تقييم السيرة الذاتية')
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::SevenExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('إغلاق')
                    ->modalContent(function (Resume $record) {
                        $reportLines = [
                            "**" . __('filament.fields.ai_score') . ": {$record->ai_score}/100**",
                            "",
                            "**" . __('filament.fields.ai_recommendation') . ": {$record->ai_recommendation}**",
                            "",
                            "**" . __('filament.fields.ai_summary') . ":**",
                            $record->ai_summary ?? '',
                            "",
                            "**" . __('filament.fields.ai_report') . ":**",
                            $record->ai_report ?? '',
                        ];
                        
                        return new \Illuminate\Support\HtmlString('<div class="prose max-w-none dark:prose-invert" style="max-height: 70vh; overflow-y: auto; padding: 1rem;">' . \Illuminate\Support\Str::markdown(implode("\n", $reportLines)) . '</div>');
                    })
                    ->visible(fn (Resume $record) => $record->analyzed_at !== null),
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
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.resumes');
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
