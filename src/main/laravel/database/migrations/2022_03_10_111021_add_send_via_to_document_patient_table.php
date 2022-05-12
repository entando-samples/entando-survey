<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendViaToDocumentPatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_patient', function (Blueprint $table) {
            $table->enum('send_via',['DIRECT','SCHEDULED'])->after('is_read')->default('SCHEDULED');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_patient', function (Blueprint $table) {
            $table->dropColumn('send_via');
        });
    }
}
