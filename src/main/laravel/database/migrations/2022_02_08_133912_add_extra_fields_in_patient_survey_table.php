<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsInPatientSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_survey', function (Blueprint $table) {
            $table->timestamp('reviewed_at')
                ->after('completed_at')
                ->nullable();
            $table->text('note')
                ->after('reviewed_at')
                ->nullable();
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
            $table->dropColumn('reviewed_at');
            $table->dropColumn('note');
        });
    }
}
