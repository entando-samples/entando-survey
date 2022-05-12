<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentPatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
                ->constrained('documents', 'id')
                ->onDelete('cascade');
            $table->foreignId('patient_id')
                ->constrained('users', 'id')
                ->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->unique(['document_id', 'patient_id']);
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
        Schema::dropIfExists('document_patient');
    }
}
