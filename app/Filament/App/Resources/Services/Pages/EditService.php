<?php

namespace App\Filament\App\Resources\Services\Pages;

use App\Filament\App\Resources\Services\ServiceResource;
use App\Filament\Shared\Pages\EditRecordRedirectToIndex;
use Filament\Actions\DeleteAction;

class EditService extends EditRecordRedirectToIndex
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
