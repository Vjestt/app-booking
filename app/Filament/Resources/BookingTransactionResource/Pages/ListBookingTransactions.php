<?php

namespace App\Filament\Resources\BookingTransactionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BookingTransactionResource;
use App\Filament\Resources\BookingTransactionResource\Widgets\BookingTransactionStats;

class ListBookingTransactions extends ListRecords
{
    protected static string $resource = BookingTransactionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BookingTransactionStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
