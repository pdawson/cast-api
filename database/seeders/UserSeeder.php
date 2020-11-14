<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->createUser('me@pauldawson.me', 'Paul Dawson', 'secret');
    }

    /**
     * Creates or updates a new user model
     *
     * @param string $email
     * @param string $name
     * @param string $password
     * @return User
     */
    protected function createUser(string $email, string $name, string $password): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password)
            ]
        );
    }
}
