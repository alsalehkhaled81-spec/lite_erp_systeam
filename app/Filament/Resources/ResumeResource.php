<?php

namespace App\Filament\Resources;

use App\Filament\Hr\Resources\ResumeResource as HrResumeResource;
use App\Filament\Resources\ResumeResource\Pages;

class ResumeResource extends HrResumeResource
{
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResumes::route('/'),
            'create' => Pages\CreateResume::route('/create'),
            'edit' => Pages\EditResume::route('/{record}/edit'),
        ];
    }
}
