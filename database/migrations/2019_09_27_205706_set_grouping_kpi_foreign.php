<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetGroupingKpiForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groupingkpi', function (Blueprint $table) {
            $table->foreign('tag_id')->references('id')->on('kpitags')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groupingkpi', function (Blueprint $table) {
            $table->dropForeign('groupingkpi_tag_id_foreign');
            $table->dropForeign('groupingkpi_employee_id_foreign');
        });
    }
}
