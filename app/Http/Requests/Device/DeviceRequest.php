<?php

namespace App\Http\Requests\Device;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deviceId = $this->route('device')?->id ?? $this->route('device');

        return [
            'device_code' => ['required', 'string', 'max:100', Rule::unique('devices', 'device_code')->ignore($deviceId)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([Device::TYPE_POS, Device::TYPE_MOBILE, Device::TYPE_ADMIN_PANEL])],
            'last_seen_at' => ['nullable', 'date'],
            'active' => ['required', 'boolean'],
        ];
    }
}
