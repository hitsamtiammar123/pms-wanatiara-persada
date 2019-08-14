<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetForeignKeyKpiResultDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpiresultsheader', function (Blueprint $table) {
            //
            $table->foreign('kpi_result_id')->references('id')
            ->on('kpiresults')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('kpi_header_id')->references('id')
            ->on('kpiheaders')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpiresultsheader', function (Blueprint $table) {
            //
            $table->dropForeign('kpiresultsheader_kpi_result_id_foreign');
            $table->dropForeign('kpiresultsheader_kpi_header_id_foreign');
        });
    }
}
