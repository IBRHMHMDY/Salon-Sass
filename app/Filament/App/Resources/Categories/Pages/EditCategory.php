<?php

namespace App\Filament\App\Resources\Categories\Pages;

use App\Filament\App\Resources\Categories\CategoryResource;
use App\Filament\Shared\Pages\EditRecordRedirectToIndex;
use Filament\Actions\DeleteAction;

class EditCategory extends EditRecordRedirectToIndex
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
