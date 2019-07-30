<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetForeignKeyKpiResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpiresults', function (Blueprint $table) {
            //
            $table->foreign('kpi_header_id')->references('id')->on('kpiheaders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpiresults', function (Blueprint $table) {
            //
            $table->dropForeign('kpiresults_kpi_header_id_foreign');
        });
    }
}
