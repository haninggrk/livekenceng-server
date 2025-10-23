<?php

namespace Database\Seeders;

use App\Models\SoftwareUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoftwareUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample update for live-kenceng
        SoftwareUpdate::create([
            'target' => 'live-kenceng',
            'version' => '0.1.2',
            'notes' => 'Bug fixes and improvements',
            'pub_date' => now(),
            'platforms' => [
                'windows-x86_64' => [
                    'signature' => 'dW50cnVzdGVkIGNvbW1lbnQ6IG1pbmlzaWduIHB1YmxpYyBrZXk6IEJGNjA1RkQ2QkVDREYxQ0YKUldUUDhjMisxbDlndjFTNW8ya216L29iS2NOaHZsV015WnpPcUdCUGFycWdZYjJKWVE5WnZyTDQK',
                    'url' => 'https://livekenceng.com/releases/live-kenceng_0.1.2_x64_en-US.msi'
                ]
            ],
            'is_active' => true
        ]);
    }
}
