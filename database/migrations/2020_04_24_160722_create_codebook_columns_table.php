<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodebookColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codebookcolumns', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');
            $table->unsignedBigInteger('codebook_id')->nullable();
            $table->foreign('codebook_id')->references('id')->on('codebook');
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
        Schema::dropIfExists('codebookcolumns');
    }
}
