<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run()
    {
        User::create([
            'name' => 'Admin TU',
            'email' => 'admintu@gmail.test',
            'password' => Hash::make('qazwsxedc'), // ganti password sesuai kebutuhan
        ]);
    }
}
