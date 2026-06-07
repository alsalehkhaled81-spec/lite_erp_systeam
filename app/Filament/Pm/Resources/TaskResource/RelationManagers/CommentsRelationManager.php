<?php

namespace App\Filament\Pm\Resources\TaskResource\RelationManagers;

use App\Models\TaskComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.task_comments');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('comment')
                ->label(__('filament.fields.comment'))
                ->required()
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.comment_by'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label(__('filament.fields.comment'))
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('filament.actions.add_comment'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
