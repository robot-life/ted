<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            // this is twitter's ID that we want to reference globally
            // but can't be primary key because must be null before processing
            $table->bigInteger('id')->unsigned()->unique()->nullable();
            $table->increments('dbid');
            $table->text('json');
            $table->dateTimeTz('created_at')->nullable();
            $table->text('text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tweets');
    }
}
