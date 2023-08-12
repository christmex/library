<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = '@sekolahbasic.sch.id';
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super'.$domain,
                'password' => bcrypt('mantapjiwa00')
            ],
            [
                'name' => 'Perpustakaan Batam Center',
                'email' => 'adminbc'.$domain,
                'password' => bcrypt('adminadminbro')
            ],
            [
                'name' => 'Perpustakaan Batu Aji',
                'email' => 'adminbta'.$domain,
                'password' => bcrypt('adminadminjugabro')
            ]
        ];
        DB::table('users')->insertOrIgnore($users);
    }
}
