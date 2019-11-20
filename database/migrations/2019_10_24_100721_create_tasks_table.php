<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('state_id');
            $table->string('name');
            $table->text('overview');
            $table->tinyInteger('priority')->comment('優先度');
            $table->tinyInteger('difficulty')->comment('難易度');
            $table->datetime('dead_line')->comment('締切');
            $table->timestamps();
            
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('state_id')->references('id')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
