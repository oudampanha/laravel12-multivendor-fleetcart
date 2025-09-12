<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $roles = [
      [
        'id'    => 1,
        'title' => 'Super Admin',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 2,
        'title' => 'Admin',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 3,
        'title' => 'Librarian',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 4,
        'title' => 'Member',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 5,
        'title' => 'User',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 6,
        'title' => 'Staff',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 7,
        'title' => 'Guest',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 8,
        'title' => 'Manager',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 9,
        'title' => 'Editor',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'id'    => 10,
        'title' => 'Viewer',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ];

    Role::insert($roles);
  }
}
