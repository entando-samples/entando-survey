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
        Schema::create('medical_data', function (Blueprint $table) {
            $table->id();

            $table->timestamp('initial_date')->nullable();

            $table->foreignId('pathology_id')
                ->nullable()
                ->constrained('pathologies')
                ->onDelete('set null');

            $table->foreignId('doctor_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->foreignId('patient_id')
                ->constrained('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('medical_data');
    }
};
