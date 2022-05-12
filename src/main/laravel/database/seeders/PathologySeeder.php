<?php

namespace Database\Seeders;

use App\Models\Pathology;
use Illuminate\Database\Seeder;

class PathologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ['Cancro ai polmoni', 'Cancro al rene', 'Cancro alla prostata', 'Cancro al fegato', 'Cancro al cervello', 'Cancro alla trachea'];

        foreach ($data as $title) {
            Pathology::updateOrCreate([
                'title' => $title,
            ], []);
        }
    }
}
