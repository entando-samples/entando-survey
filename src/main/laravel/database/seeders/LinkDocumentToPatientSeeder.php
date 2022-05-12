<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class LinkDocumentToPatientSeeder extends Seeder
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
            ->documents()
            ->sync(Document::pluck('id')->toArray());
    }
}
