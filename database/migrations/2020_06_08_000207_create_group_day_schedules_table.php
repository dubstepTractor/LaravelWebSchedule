<?php

use App\Objects\Schedule\Day\GroupDaySchedule;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupDaySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(GroupDaySchedule::TABLE, function (Blueprint $table) {
            $table->id();
            $table->integer(GroupDaySchedule::INDEX_TARGET_ID);
            $table->integer(GroupDaySchedule::INDEX_FIRST_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_SECOND_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_THIRD_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_FOURTH_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_FIFTH_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_SIXTH_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_SEVENTH_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_EIGHTH_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_NINTH_SUBJECT_ID)->nullable();
            $table->integer(GroupDaySchedule::INDEX_TENTH_SUBJECT_ID)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(GroupDaySchedule::TABLE);
    }
}
