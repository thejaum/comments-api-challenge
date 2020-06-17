<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableHighlightComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('highlight_comment', function (Blueprint $table) {
            $table->bigIncrements('id_highlight_comment');
            $table->unsignedBigInteger('id_comment');
            $table->dateTime('expiration_date');
            $table->foreign('id_comment')->references('id_comment')->on('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('highlight_comment');
    }
}
