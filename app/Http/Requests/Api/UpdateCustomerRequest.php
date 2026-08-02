<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'pic_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pic_phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'pic_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'service_id' => ['sometimes', 'nullable', 'exists:services,id'],
            'project_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'payment_status' => ['sometimes', 'required', 'in:unpaid,partial,paid'],
        ];
    }
}
