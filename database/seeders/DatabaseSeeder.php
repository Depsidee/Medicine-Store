<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\GenericName;
use \App\Models\Company;
use \App\Models\Category;
use \App\Models\Product;
use \App\Models\User;
use \App\Models\Device;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        GenericName::factory(20)->create();
        Company::factory(20)->create();
        Category::factory(20)->create();
        Product::factory(50)->create();
        $user = User::create([
            'firstName'=>'omar',
            'lastName'=>'konan',
            'role'=>'Admin',
            'phoneNumber'=>'0956537577',
            'password'=>Hash::make('12345678')
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
