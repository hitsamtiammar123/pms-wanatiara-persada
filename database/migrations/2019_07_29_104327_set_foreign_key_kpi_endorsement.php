<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetForeignKeyKpiEndorsement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpiendorsements', function (Blueprint $table) {
            //
            $table->foreign('kpi_header_id')->references('id')->on('kpiheaders')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpiendorsements', function (Blueprint $table) {
            //
            $table->dropForeign('kpiendorsements_kpi_header_id_foreign');
            $table->dropForeign('kpiendorsements_employee_id_foreign');
        });
    }
}
