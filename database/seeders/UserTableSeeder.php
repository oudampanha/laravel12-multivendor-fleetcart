<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $users = [
      [
        'first_name'           => 'Samnang',
        'last_name'            => 'Tech',
        'username'       => 'superadmin',
        'phone_no'       => '078343143',
        'email'          => 'superadmin@gmail.com',
        'password'       => bcrypt('12345678'),
        'remember_token' => null,
      ],
    ];

    User::insert($users);
  }
}
