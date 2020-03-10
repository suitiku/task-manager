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
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('project_id');
            $table->string('name');
            $table->text('overview')->nullable();
            $table->tinyInteger('priority')->comment('優先度');
            $table->tinyInteger('difficulty')->comment('難易度');
            $table->datetime('start_date')->comment('開始日時')->default(date('Y-m-d H:i:s'));
            $table->datetime('dead_line')->comment('締切');
            $table->boolean('is_template')->default(false)->comment('テンプレートファイル');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
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
