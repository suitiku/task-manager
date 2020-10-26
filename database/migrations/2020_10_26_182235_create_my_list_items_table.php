<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyListItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_list_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('my_list_id');
            $table->smallInteger('index')->comment('アイテムの位置情報');
            $table->json('values')->comment('列の内容を示すJSONデータ');
            $table->boolean('is_checked')->comment('チェックボックス用');
            $table->boolean('is_stared')->comment('スター付ける用');
            $table->timestamps();
            
            $table->foreign('my_list_id')->references('id')->on('my_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_list_items');
    }
}
