<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            "email" => "admin@kotuko.it"
        ], [
            'name' => "Kotuko",
            "password" => bcrypt("admin123"),
            "role" => User::ADMIN
        ]);

        User::factory()->count(10)->create([
            "role" => User::PATIENT
        ]);

        User::create([
            'name'=>'Aashish Aryal',
            'email'=>'aashish.aryal@kotuko.it',
            'password'=>bcrypt("password"),
            "role"=>User::DOCTOR
        ]);
        User::create([
            'name'=>'Doctor Alessio',
            'email'=>'alessio.ravani@kotuko.it',
            'password'=>bcrypt("password"),
            "role"=>User::DOCTOR
        ]);

        User::factory()->count(5)->create([
            "role" => User::DOCTOR,
            'email_report'=>false,
        ]);
    }
}
