<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeightingInKpiHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpiheaders', function (Blueprint $table) {
            //
            $table->float('weight_result')->default(0.6);
            $table->float('weight_process')->default(0.4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpiheaders', function (Blueprint $table) {
            //
            $table->dropColumn('weight_result');
            $table->dropColumn('weight_process');
        });
    }
}
