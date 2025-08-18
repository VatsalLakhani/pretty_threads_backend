<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'Admin@123456');

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'full_name' => 'Administrator',
                'phone_number' => '0000000000',
                'password' => Hash::make($password),
                'full_address' => 'HQ',
                'city' => 'City',
                'pincode' => '000000',
                'is_admin' => true,
            ]
        );

        // Ensure is_admin flag stays true if user existed
        if (! $admin->is_admin) {
            $admin->is_admin = true;
            $admin->save();
        }
    }
}
