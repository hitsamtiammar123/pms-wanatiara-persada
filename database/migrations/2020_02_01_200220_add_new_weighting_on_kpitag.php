<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewWeightingOnKpitag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpitags', function (Blueprint $table) {
            //
            $table->float('weight_result')->default(0.5);
            $table->float('weight_process')->default(0.5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpitags', function (Blueprint $table) {
            //
            $table->dropColumn('weight_result');
            $table->dropColumn('weight_process');
        });
    }
}
