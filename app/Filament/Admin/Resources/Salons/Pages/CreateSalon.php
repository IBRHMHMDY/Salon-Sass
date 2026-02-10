<?php

namespace App\Filament\Admin\Resources\Salons\Pages;

use App\Filament\Admin\Resources\Salons\SalonResource;
use App\Filament\Shared\Pages\CreateRecordRedirectToIndex;

class CreateSalon extends CreateRecordRedirectToIndex
{
    protected static string $resource = SalonResource::class;
}
