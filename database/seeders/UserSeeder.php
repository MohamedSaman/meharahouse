<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@meharahouse.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'phone'    => '+251 911 000 001',
                'address'  => 'Bole Road, Addis Ababa, Ethiopia',
            ]
        );

        // Staff
        User::updateOrCreate(
            ['email' => 'staff@meharahouse.com'],
            [
                'name'     => 'Staff Member',
                'password' => Hash::make('password'),
                'role'     => 'staff',
                'phone'    => '+251 911 000 002',
                'address'  => 'Piazza, Addis Ababa, Ethiopia',
            ]
        );

        // Sample customers
        $customers = [
            ['name' => 'Abebe Kebede',  'email' => 'abebe@example.com',  'phone' => '+251 911 111 001'],
            ['name' => 'Tigist Hailu',  'email' => 'tigist@example.com', 'phone' => '+251 911 111 002'],
            ['name' => 'Solomon Tadesse','email' => 'solomon@example.com','phone' => '+251 911 111 003'],
            ['name' => 'Marta Alemu',   'email' => 'marta@example.com',  'phone' => '+251 911 111 004'],
            ['name' => 'Dawit Bekele',  'email' => 'dawit@example.com',  'phone' => '+251 911 111 005'],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name'     => $customer['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'customer',
                    'phone'    => $customer['phone'],
                ]
            );
        }
    }
}
