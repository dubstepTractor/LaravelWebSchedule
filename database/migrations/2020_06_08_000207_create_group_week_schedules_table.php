<?php

use App\Objects\Schedule\Week\GroupWeekSchedule;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupWeekSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(GroupWeekSchedule::TABLE, function (Blueprint $table) {
            $table->id();
            $table->integer(GroupWeekSchedule::INDEX_TARGET_ID);
            $table->integer(GroupWeekSchedule::INDEX_FIRST_SCHEDULE_ID)->nullable();
            $table->integer(GroupWeekSchedule::INDEX_SECOND_SCHEDULE_ID)->nullable();
            $table->integer(GroupWeekSchedule::INDEX_THIRD_SCHEDULE_ID)->nullable();
            $table->integer(GroupWeekSchedule::INDEX_FOURTH_SCHEDULE_ID)->nullable();
            $table->integer(GroupWeekSchedule::INDEX_FIFTH_SCHEDULE_ID)->nullable();
            $table->integer(GroupWeekSchedule::INDEX_SIXTH_SCHEDULE_ID)->nullable();
            $table->integer(GroupWeekSchedule::INDEX_SEVENTH_SCHEDULE_ID)->nullable();
            #table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(GroupWeekSchedule::TABLE);
    }
}
