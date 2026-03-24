<?php

namespace Database\Seeders\Demo;

use App\Models\Device;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        Device::query()->updateOrCreate(
            ['device_code' => 'POS-LOCAL-01'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'POS Principal',
                'type' => Device::TYPE_POS,
                'active' => true,
                'last_seen_at' => now(),
            ]
        );
    }
}
