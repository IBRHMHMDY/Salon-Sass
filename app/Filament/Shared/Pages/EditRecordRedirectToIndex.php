<?php

namespace App\Filament\Shared\Pages;

use Filament\Resources\Pages\EditRecord;

abstract class EditRecordRedirectToIndex extends EditRecord
{
    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }
}
