<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name' => '田中太郎',
            'email' => 'test@gmail.com',
            'profile' => '中小企業向けの経営コンサルタントとして働く',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }
}
