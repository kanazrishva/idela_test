<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatasetMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datasets_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('datasets_id');
            $table->foreign('datasets_id')->references('id')->on('datasets');
            $table->string('column_name');
            $table->string('value')->nullable();
            $table->unsignedBigInteger('codebook_id')->nullable();
            $table->foreign('codebook_id')->references('id')->on('codebook');
            $table->unsignedBigInteger('row');
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
        Schema::dropIfExists('dataset_meta');
    }
}
