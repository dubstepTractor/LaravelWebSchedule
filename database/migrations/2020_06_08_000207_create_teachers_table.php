<?php

use App\Objects\Base\Teacher;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Teacher::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string(Teacher::INDEX_NAME);
            $table->string(Teacher::INDEX_SURNAME);
            $table->string(Teacher::INDEX_PATRONYMIC)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Teacher::TABLE);
    }
}
