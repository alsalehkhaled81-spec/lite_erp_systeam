<?php

namespace App\Filament\Resources\VacancyResource\RelationManagers;

use App\Models\Resume;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class ApplicantsRelationManager extends RelationManager
{
    protected static string $relationship = 'applicants';

    protected static ?string $title = 'المتقدمون للوظيفة';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('job_title')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('salary')
                    ->label(__('filament.fields.expected_salary_short'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
                Tables\Columns\TextColumn::make('resume.file_path')
                    ->label(__('filament.fields.resume_file'))
                    ->url(fn ($record) => $record->resume && $record->resume->file_path ? asset('storage/' . $record->resume->file_path) : null)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($record) => $record->resume ? __('filament.actions.download_view') : '—'),
                Tables\Columns\TextColumn::make('resume.ai_score')
                    ->label(__('filament.fields.ai_score'))
                    ->badge()
                    ->color(fn ($state): string => $state === null ? 'gray' : ($state >= 70 ? 'success' : ($state >= 40 ? 'warning' : 'danger')))
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.application_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.fields.application_status'))
                    ->options([
                        'pending' => __('filament.status.application_pending'),
                        'active' => __('filament.status.application_active'),
                        'rejected' => __('filament.status.application_rejected'),
                    ]),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('view_resume')
                    ->label(__('filament.actions.view_resume'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn ($record) => $record->resume && $record->resume->file_path ? asset('storage/' . $record->resume->file_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->resume && $record->resume->file_path),

                Tables\Actions\Action::make('analyze_resume')
                    ->label(__('filament.actions.ai_analyze_resume'))
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->modalHeading(__('filament.actions.ai_analyze_resume_heading'))
                    ->modalDescription(__('filament.actions.ai_analyze_resume_desc'))
                    ->modalSubmitActionLabel(__('filament.actions.start_analysis'))
                    ->fillForm(fn ($record, RelationManager $livewire): array => [
                        'target_job_title' => $livewire->getOwnerRecord()->title ?? $record->job_title ?? '',
                        'job_keywords' => $livewire->getOwnerRecord()->requirements ?? '',
                    ])
                    ->form([
                        Forms\Components\TextInput::make('target_job_title')
                            ->label('المسمى الوظيفي المستهدف')
                            ->placeholder('مثال: مطور PHP')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('job_keywords')
                            ->label(__('filament.fields.job_keywords'))
                            ->placeholder(__('filament.fields.job_keywords_placeholder'))
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function ($record, array $data) {
                        $employee = $record;
                        $resume = $employee->resume;
                        
                        if (!$resume) {
                            Notification::make()
                                ->title('خطأ')
                                ->body('لا توجد سيرة ذاتية لهذا المتقدم.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $employee->load(['user', 'department', 'skills']);

                        $skillsList = $employee->skills->pluck('name')->implode(', ');

                        $resumeData = [
                            'employee_name' => $employee->user?->name ?? 'غير محدد',
                            'job_title' => $employee->job_title ?? 'غير محدد',
                            'department' => $employee->department?->name ?? 'غير محدد',
                            'salary' => $employee->salary ?? 'غير محدد',
                            'status' => $employee->status ?? 'غير محدد',
                            'skills' => $skillsList ?: 'لا توجد مهارات مسجلة',
                            'resume_text' => $resume->resume_text ?? 'لا يوجد نص للسيرة الذاتية',
                        ];

                        $service = app(\App\Services\ResumeAnalysisService::class);
                        $result = $service->analyzeResume($resumeData, $data['job_keywords'], $data['target_job_title']);

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

                        $resume->update([
                            'ai_score' => $result['score'] ?? null,
                            'ai_summary' => $result['summary'] ?? null,
                            'ai_report' => $result['report'] ?? null,
                            'ai_recommendation' => $result['recommendation'] ?? null,
                            'analyzed_at' => now(),
                        ]);

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
                            ->body(new \Illuminate\Support\HtmlString(\Illuminate\Support\Str::markdown(implode("\n", $reportLines))))
                            ->{$color}()
                            ->persistent()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->resume !== null),

                Tables\Actions\Action::make('accept')
                    ->label(__('filament.actions.accept_employment'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'active', 'hire_date' => now()]);
                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\JobApplicationStatusNotification('active', $record->user->name));
                        }
                        Notification::make()->title(__('filament.notifications.applicant_accepted'))->success()->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label(__('filament.actions.reject_request'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('filament.actions.rejection_reason'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason']]);
                        if ($record->user) {
                            $record->user->notify(new \App\Notifications\JobApplicationStatusNotification('rejected', $record->user->name));
                        }
                        Notification::make()->title(__('filament.notifications.applicant_rejected'))->danger()->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
