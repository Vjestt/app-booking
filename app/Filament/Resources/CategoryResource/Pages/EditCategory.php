<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CategoryResource;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->after(function(Category $icon){
                if ($icon->photo) {
             Storage::disk('public')->delete($icon->photo);
          }
            }),
        ];
    }
}
