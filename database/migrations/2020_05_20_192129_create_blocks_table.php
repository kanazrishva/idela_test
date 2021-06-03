<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('pages', function (Blueprint $table) {
         $table->text('blocks')->nullable()->after('publish');
         $table->text('meta')->nullable()->after('publish');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('pages', function (Blueprint $table) {
        //
        $table->dropColumn('blocks');
        $table->dropColumn('meta');
        
      });
    }
}
