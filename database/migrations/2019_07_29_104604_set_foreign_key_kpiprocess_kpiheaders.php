<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetForeignKeyKpiprocessKpiheaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpiprocesses_kpiheaders', function (Blueprint $table) {
            //
            $table->foreign('kpi_proccess_id')->references('id')
            ->on('kpiprocesses')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('kpiprocesses_kpiheaders', function (Blueprint $table) {
            //
            $table->dropForeign('kpiprocesses_kpiheaders_kpi_proccess_id_foreign');
            $table->dropForeign('kpiprocesses_kpiheaders_kpi_header_id_foreign');
        });
    }
}
