<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısı
        User::firstOrCreate([
            'email' => 'admin@eterna.net.tr'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'Eterna',
            'phone' => '5559876542',
            'role_id' => 1, // Admin role
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);

        // Yazar kullanıcıları
        User::firstOrCreate([
            'email' => 'writer@eterna.net.tr'
        ], [
            'first_name' => 'Writer',
            'last_name' => 'Eterna',
            'phone' => '5559876543',
            'role_id' => 2, // Writer role
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'writer2@eterna.net.tr'
        ], [
            'first_name' => 'Writer',
            'last_name' => 'Eterna',
            'phone' => '5559876544',
            'role_id' => 2, // Writer role
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);

        // Normal kullanıcılar
        User::firstOrCreate([
            'email' => 'user@eterna.net.tr'
        ], [
            'first_name' => 'User',
            'last_name' => 'Eterna',
            'phone' => '5559876545',
            'role_id' => 3, // User role
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'user2@eterna.net.tr'
        ], [
            'first_name' => 'User',
            'last_name' => 'Eterna',
            'phone' => '5559876546',
            'role_id' => 3, // User role
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);

        // Test Users
        User::factory(8)->create([
            'role_id' => 3, // Normal Users
            'email_verified_at' => now(),
        ]);

        // Test Writers
        User::factory(3)->create([
            'role_id' => 2, // Writer role
            'email_verified_at' => now(),
        ]);
    }
} 