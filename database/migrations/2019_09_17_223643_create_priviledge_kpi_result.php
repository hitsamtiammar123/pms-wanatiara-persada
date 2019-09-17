<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriviledgeKpiResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('priviledgekpiresults', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('value');
            $table->string('by',50)->default('name');
            $table->integer('priviledge')->default(1);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('priviledgekpiresults');
    }
}
