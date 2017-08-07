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
            // reuse twitter's ID (do not increment/generate)
            $table->bigInteger('id')->unsigned();
            $table->primary('id');

            $table->text('json');
            $table->dateTimeTz('created_at');
            $table->text('text');

            $table->decimal('sentiment', 15, 14);
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
