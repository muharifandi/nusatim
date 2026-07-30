<?php

namespace App\Filament\Pages;

use App\Models\Partner;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SendAnnouncement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationLabel = 'Pengumuman';

    protected static ?string $navigationGroup = 'Partner Program';

    protected static string $view = 'filament.pages.send-announcement';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['recipients' => 'all']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required()->maxLength(255),
                RichEditor::make('body')->required(),
                Radio::make('recipients')
                    ->options([
                        'all' => 'Semua Partner (approved)',
                        'specific' => 'Partner Tertentu',
                    ])
                    ->default('all')
                    ->live()
                    ->required(),
                Select::make('partner_ids')
                    ->label('Pilih Partner')
                    ->multiple()
                    ->options(fn () => Partner::where('status', 'approved')->pluck('name', 'id'))
                    ->visible(fn (Get $get) => $get('recipients') === 'specific')
                    ->required(fn (Get $get) => $get('recipients') === 'specific'),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $partners = $data['recipients'] === 'all'
            ? Partner::where('status', 'approved')->get()
            : Partner::whereIn('id', $data['partner_ids'] ?? [])->get();

        foreach ($partners as $partner) {
            Notification::make()
                ->title($data['title'])
                ->body($data['body'])
                ->sendToDatabase($partner);
        }

        Notification::make()
            ->title("Pengumuman terkirim ke {$partners->count()} partner")
            ->success()
            ->send();

        $this->form->fill(['recipients' => 'all']);
    }
}
