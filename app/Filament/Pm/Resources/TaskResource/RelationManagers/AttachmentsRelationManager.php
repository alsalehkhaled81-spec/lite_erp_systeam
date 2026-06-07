<?php

namespace App\Filament\Pm\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.task_attachments');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('file_path')
                ->label(__('filament.fields.upload_file'))
                ->required()
                ->disk('public')
                ->directory('task-attachments')
                ->preserveFilenames()
                ->downloadable()
                ->maxSize(10240)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('file_name')
                    ->label(__('filament.fields.file_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_type')
                    ->label(__('filament.fields.file_type'))
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('file_size')
                    ->label(__('filament.fields.file_size'))
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1) . ' KB' : '-'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.uploaded_by'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('filament.actions.upload_attachment'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        $data['file_name'] = $data['file_path'];
                        $filePath = Storage::disk('public')->path($data['file_path']);
                        if (file_exists($filePath)) {
                            $data['file_size'] = filesize($filePath);
                            $data['file_type'] = pathinfo($filePath, PATHINFO_EXTENSION);
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label(__('filament.actions.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        Storage::disk('public')->delete($record->file_path);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
