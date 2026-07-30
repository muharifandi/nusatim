<?php

namespace App\Console\Commands;

use App\Models\LeadReminder;
use Filament\Notifications\Notification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reminders:notify-due')]
#[Description('Send a database notification to the owning partner for any lead reminder whose time has arrived.')]
class NotifyDueReminders extends Command
{
    public function handle(): int
    {
        $reminders = LeadReminder::query()
            ->whereNull('completed_at')
            ->whereNull('notified_at')
            ->where('remind_at', '<=', now())
            ->get();

        if ($reminders->isEmpty()) {
            $this->info('No due reminders.');

            return self::SUCCESS;
        }

        foreach ($reminders as $reminder) {
            $partner = $reminder->lead->partner;

            if ($partner) {
                $label = $reminder->type === 'meeting' ? 'Meeting' : 'Follow Up';

                Notification::make()
                    ->title("Reminder {$label}: {$reminder->lead->name}")
                    ->body($reminder->note)
                    ->sendToDatabase($partner);
            }

            $reminder->update(['notified_at' => now()]);
        }

        $this->info("Notified {$reminders->count()} due reminder(s).");

        return self::SUCCESS;
    }
}
