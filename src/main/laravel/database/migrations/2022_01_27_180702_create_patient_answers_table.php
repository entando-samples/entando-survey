<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("survey_id")
                ->constrained("surveys");

            // i know its repeated but it makes life a lot easier
            $table->foreignId("question_id")
                ->constrained("questions");

            $table->foreignId("answer_id")
                ->constrained("answers");

            $table->string("patient_id");


            $table->unique(['survey_id', 'question_id', 'patient_id']);
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
        Schema::dropIfExists('patient_answers');
    }
}
