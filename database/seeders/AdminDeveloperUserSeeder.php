<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminDeveloperUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMINDEVELOPER_EMAIL');
        $password = env('ADMINDEVELOPER_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('Admin developer account skipped: set ADMINDEVELOPER_EMAIL and ADMINDEVELOPER_PASSWORD.');

            return;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'fname' => 'Developer',
                'lname' => 'Administrator',
                'password' => Hash::make($password),
                'role' => 'admindeveloper',
            ]
        );
    }
}
