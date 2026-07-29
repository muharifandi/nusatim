<?php

namespace App\Filament\Partner\Resources\WithdrawalResource\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Partner\Resources\WithdrawalResource;
use App\Models\Withdrawal;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateWithdrawal extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = WithdrawalResource::class;

    /**
     * Goes through Withdrawal::submit() instead of a plain create() so the
     * balance/minimum validation actually runs server-side - the form only
     * displays the balance, it doesn't enforce it.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return Withdrawal::submit(Auth::guard('partner')->user(), $data);
    }
}
