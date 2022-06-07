<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_survey', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id');

            $table->foreignId('survey_id')
                ->constrained('surveys')
                ->onDelete('cascade');
            $table->timestamp('completed_at')
                ->nullable();
            $table->unique(['survey_id', 'patient_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_survey');
    }
}
