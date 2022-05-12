<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePathologySurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pathology_survey', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pathology_id')
                ->constrained('pathologies');

            $table->foreignId('survey_id')
                ->constrained('surveys');

            $table->unique(['survey_id', 'pathology_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pathology_survey');
    }
}
