<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->text('players');
            $table->text('lucky');
            $table->text('red');
            $table->text('orange');
            $table->text('yellow');
            $table->text('green');
            $table->text('cyan');
            $table->text('blue');
            $table->text('violet');
            $table->enum('win_sector',['lucky','red','orange','yellow','green','cyan','blue',
                'violet']);
            $table->text('winners');
            $table->dateTime('finished_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('games');
    }
}
