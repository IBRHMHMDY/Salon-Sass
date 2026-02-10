<?php

namespace App\Filament\App\Resources\Categories\Pages;

use App\Filament\App\Resources\Categories\CategoryResource;
use App\Filament\Shared\Pages\CreateRecordRedirectToIndex;

class CreateCategory extends CreateRecordRedirectToIndex
{
    protected static string $resource = CategoryResource::class;
}
