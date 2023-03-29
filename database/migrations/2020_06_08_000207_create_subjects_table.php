<?php

use App\Objects\Base\Subject;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Subject::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string(Subject::INDEX_NAME);
            $table->string(Subject::INDEX_CLASSROOM)->nullable();
            $table->string(Subject::INDEX_TEACHER_ID)->nullable();
            $table->string(Subject::INDEX_SECOND_TEACHER_ID)->nullable();
            $table->string(Subject::INDEX_COMMENTARY)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Subject::TABLE);
    }
}
