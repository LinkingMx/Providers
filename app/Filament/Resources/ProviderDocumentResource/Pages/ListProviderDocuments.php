<?php

namespace App\Filament\Resources\ProviderDocumentResource\Pages;

use App\Filament\Resources\ProviderDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProviderDocuments extends ListRecords
{
    protected static string $resource = ProviderDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
