<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // options which on being selected should alert
        Schema::create('survey_warning_ans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys', 'id');

            $table->foreignId('answer_id')
                ->constrained('answers', 'id');

            $table->unique(['survey_id', 'answer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_warning_ans');
    }
};
