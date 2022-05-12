<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(AppSettingSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PathologySeeder::class);
        $this->call(DocumentSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(SurveySeeder::class);
        $this->call(MessageSeeder::class);
        $this->call(LinkDocumentToPatientSeeder::class);
        $this->call(PatientDocumentSeeder::class);
        $this->call(LinkSurveyToPatientSeeder::class);
    }
}
