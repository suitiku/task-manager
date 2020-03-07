<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('state_task', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('state_id');
            $table->unsignedInteger('task_id');
            $table->string('state_detail')->comment('状態の詳細')->nullable();
            $table->timestamps();
            
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('task_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('state_task');
    }
}
