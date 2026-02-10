<?php

namespace App\Filament\Shared\Pages;

use Filament\Resources\Pages\CreateRecord;

abstract class CreateRecordRedirectToIndex extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
