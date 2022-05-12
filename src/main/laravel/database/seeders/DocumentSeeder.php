<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Upload;
use App\Models\Document;
use App\Models\Pathology;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Document::count() < 6) {
            Document::factory()
                ->count(10)
                ->afterCreating(function (Document $document) {
                    $document->pathologies()->attach(Pathology::inRandomOrder()->limit(3)->pluck('id')->toArray());
                })
                ->create([
                    'created_by' => User::first()->id,
                    'updated_by' => User::first()->id,
                ]);
        }
    }
}
