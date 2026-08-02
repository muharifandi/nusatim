<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'required', 'in:follow_up,meeting'],
            'remind_at' => ['sometimes', 'required', 'date'],
            'note' => ['sometimes', 'nullable', 'string'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
