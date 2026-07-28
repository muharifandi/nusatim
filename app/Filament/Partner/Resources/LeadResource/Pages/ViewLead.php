<?php

namespace App\Filament\Partner\Resources\LeadResource\Pages;

use App\Filament\Partner\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markWon')
                ->label('Tandai Won')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => ! in_array($this->record->status, ['won', 'lost']))
                ->action(fn () => $this->record->markWon()),
            Actions\Action::make('markLost')
                ->label('Tandai Lost')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => ! in_array($this->record->status, ['won', 'lost']))
                ->action(fn () => $this->record->markLost()),
            Actions\EditAction::make(),
        ];
    }
}
