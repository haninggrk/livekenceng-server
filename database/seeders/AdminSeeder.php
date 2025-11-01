<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@livekenceng.com')->first();
        
        if ($existingAdmin) {
            $this->command->info('Admin user already exists. Skipping creation.');
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@livekenceng.com',
            'password' => Hash::make('admin123'),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@livekenceng.com');
        $this->command->info('Password: admin123');
    }
}
