<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGraphsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graphs', function (Blueprint $table) {
            //
          $table->unsignedBigInteger('page_id')->nullable()->after('id');
          $table->foreign('page_id')->references('id')->on('pages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('graphs', function (Blueprint $table) {
            //
          $table->dropForeign('graphs_page_id_foreign');
          $table->dropColumn('page_id');
        });
    }
}
