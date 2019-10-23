<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetKpiResultForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpiresultgroup', function (Blueprint $table) {
            $table->foreign('kpi_result_id')->references('id')->on('kpiresults')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tag_id')->references('id')->on('kpitags')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpiresultgroup', function (Blueprint $table) {
            $table->dropForeign('kpiresultgroup_kpi_result_id_foreign');
            $table->dropForeign('kpiresultgroup_tag_id_foreign');
        });
    }
}
