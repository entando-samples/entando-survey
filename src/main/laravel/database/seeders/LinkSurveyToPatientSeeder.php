<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Seeder;

class LinkSurveyToPatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::patient()
            ->find(2)
            ->surveys()
            ->sync(Survey::inRandomOrder()->limit(5)->pluck('id')->toArray());
    }
}
