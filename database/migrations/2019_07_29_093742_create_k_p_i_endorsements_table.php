<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKPIEndorsementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpiendorsements', function (Blueprint $table) {
            $table->string('id',18);
            $table->string('kpi_header_id',18);
            $table->string('employee_id',15);
            $table->primary('id');
            $table->enum('level',[1,2,3])->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kpiendorsements');
    }
}
