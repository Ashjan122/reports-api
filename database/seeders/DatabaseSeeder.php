<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@lab.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        Setting::firstOrCreate(['user_id' => $user->id], [
            'lab_name'         => 'WIMPEY LABORATORIES L.L.C',
            'lab_address'      => 'Muscat, Sultanate of Oman',
            'authorized_name'  => 'Chandran Ramadasan',
            'authorized_title' => 'Lab Supervisor (Civil)',
        ]);
    }
}
