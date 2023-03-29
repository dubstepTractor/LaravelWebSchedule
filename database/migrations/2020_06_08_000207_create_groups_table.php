<?php

use App\Objects\Base\Group;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Group::TABLE, function (Blueprint $table) {
            $table->id();
            $table->integer(Group::INDEX_NUMBER);
            $table->string(Group::INDEX_LETTER);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Group::TABLE);
    }
}
