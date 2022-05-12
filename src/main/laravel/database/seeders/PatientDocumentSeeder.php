<?php

namespace Database\Seeders;

use App\Models\PatientDocument;
use App\Models\PatientFolder;
use Illuminate\Database\Seeder;

class PatientDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(range(1, 5))->each(function ($idx) {
            $folder =  PatientFolder::create(['title' => 'Test folder - ' . $idx, 'patient_id' => 2]);

            collect(range(1, 3))->each(function ($idx) use ($folder) {
                PatientDocument::create([
                    'folder_id' => $folder->id,
                    'title' => 'Test document - ' . $idx,
                    'note' => 'This is test note seeded from backend',
                    'attachments' => [
                        makeTestFile('pdf'),
                        makeTestFile('pdf'),
                        makeTestFile('pdf'),
                    ],
                    'voice_message' => makeTestFile('audio')
                ]);
            });
        });
    }
}
