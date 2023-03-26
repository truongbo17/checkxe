<?php

namespace Database\Seeders;

use App\Models\User;
use Bo\PermissionManager\App\Enum\IsAdminEnum;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            'email' => 'truongnq017@gmail.com'
        ], [
            'name'     => 'Nguyen Quang Truong',
            'is_admin' => IsAdminEnum::IS_ADMIN,
            'password' => Hash::make(123456)
        ]);
    }
}
