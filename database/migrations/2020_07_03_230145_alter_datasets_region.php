<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDatasetsRegion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      //
      Schema::table('datasets', function (Blueprint $table) {
        //
        $table->unsignedBigInteger('region_id')->nullable()->after('country_id');
        $table->foreign('region_id')->references('id')->on('regions');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      //
      Schema::table('datasets', function (Blueprint $table) {
        //
        $table->dropForeign('datasets_region_id_foreign');
        $table->dropColumn('region_id');
      });
    }
}
