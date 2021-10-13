<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class userTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('username', 'admin')->first();

        if (!$user) {
            $user = new User();
            $user->name = 'Admin';
            $user->email = 'admin@gmail.com';
            $user->username = 'admin';
            $user->password = Hash::make('123456');
            $user->role_id = 1;
            $user->save();
        }
    }
}
