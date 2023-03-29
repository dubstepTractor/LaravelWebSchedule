<?php

use App\Objects\Schedule\Day\Change\ScheduleChange;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ScheduleChange::TABLE, function (Blueprint $table) {
            $table->id();
            $table->integer(ScheduleChange::INDEX_TARGET_ID);
            $table->integer(ScheduleChange::INDEX_FIRST_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_SECOND_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_THIRD_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_FOURTH_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_FIFTH_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_SIXTH_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_SEVENTH_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_EIGHTH_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_NINTH_SUBJECT_ID)->nullable();
            $table->integer(ScheduleChange::INDEX_TENTH_SUBJECT_ID)->nullable();
            $table->date(ScheduleChange::INDEX_DATE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(ScheduleChange::TABLE);
    }
}
