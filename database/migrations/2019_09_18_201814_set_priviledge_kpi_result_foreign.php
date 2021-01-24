<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetPriviledgeKpiResultForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('priviledgeresultskpia', function (Blueprint $table) {
            $table->foreign('kpi_header_result_id')->references('id')
            ->on('kpiresultsheader')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('priviledgeresultskpia', function (Blueprint $table) {
            $table->dropForeign('priviledgeresultskpia_kpi_header_result_id_foreign');
        });
    }
}
