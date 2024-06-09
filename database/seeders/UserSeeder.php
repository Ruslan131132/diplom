<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Создание ролей
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Создание пользователя с ролью администратора
        $admin = User::create([
            'name' => 'Администратор',
            'email' => 'admin@diplom.ru',
            'password' => bcrypt('pa$$w0rd123'),
        ]);
        $admin->assignRole($adminRole);

        // Создание пользователя с ролью пользователя
        $user = User::create([
            'name' => 'Тестовый пользователь',
            'email' => 'test@diplom.ru',
            'password' => bcrypt('pa$$w0rd123'),
        ]);
        $user->assignRole($userRole);
    }
}
