<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKpiresultsheadersPriviledgekpiresultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('priviledgedetail', function (Blueprint $table) {
            $table->bigInteger('p_id')->unsigned();
            $table->string('h_id',18);
            $table->string('value')->nullable();
            $table->string('key',20);
            $table->primary(['p_id','h_id']);
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
        Schema::dropIfExists('priviledgedetail');
    }
}
