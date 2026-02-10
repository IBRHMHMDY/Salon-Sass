<?php

namespace App\Filament\App\Resources\Users\Pages;

use App\Filament\App\Resources\Users\UserResource;
use App\Filament\Shared\Pages\EditRecordRedirectToIndex;
use Filament\Actions\DeleteAction;

class EditUser extends EditRecordRedirectToIndex
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
