<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetKpiProcessForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpiprocessgroup', function (Blueprint $table) {
            $table->foreign('kpi_process_id')->references('id')->on('kpiprocesses')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('kpiprocessgroup', function (Blueprint $table) {
            $table->dropForeign('kpiprocessgroup_kpi_process_id_foreign');
            $table->dropForeign('kpiprocessgroup_tag_id_foreign');
        });
    }
}
