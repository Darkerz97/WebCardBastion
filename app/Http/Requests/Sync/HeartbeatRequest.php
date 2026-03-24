<?php

namespace App\Http\Requests\Sync;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HeartbeatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([Device::TYPE_POS, Device::TYPE_MOBILE, Device::TYPE_ADMIN_PANEL])],
        ];
    }
}
