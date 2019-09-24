<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetGroupKpiresultForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groupkpiresult', function (Blueprint $table) {
            $table->foreign('kpi_result_id')->references('id')->on('kpiresults')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groupkpiresult', function (Blueprint $table) {
            $table->dropForeign('groupkpiresult_kpi_result_id_foreign');
            $table->dropForeign('groupkpiresult_role_id_foreign');
        });
    }
}
