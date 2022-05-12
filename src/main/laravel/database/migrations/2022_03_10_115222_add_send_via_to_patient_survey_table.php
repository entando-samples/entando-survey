<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendViaToPatientSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_survey', function (Blueprint $table) {
            $table->enum('send_via',['DIRECT','SCHEDULED'])->after('survey_id')->default('SCHEDULED');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_survey', function (Blueprint $table) {
            $table->dropColumn('send_via');
            
        });
    }
}
