<?php

namespace Database\Factories;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    /**
     * Backs ktp_path with an actual file on the private disk - Filament's
     * FileUpload checks Storage::exists() when hydrating an edit form and
     * silently drops the value (making a `required()` field fail) if the
     * path doesn't really exist, which a bare string default wouldn't
     * satisfy on its own.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Partner $partner) {
            Storage::disk('partner_documents')->put($partner->ktp_path, 'fake-ktp-content');
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status' => 'pending_review',
            'ktp_path' => 'registrations/fake-ktp.jpg',
            'bank_name' => 'BCA',
            'bank_account_number' => fake()->numerify('##########'),
            'bank_account_holder' => fake()->name(),
            'agreement_accepted_at' => now(),
        ];
    }
}
