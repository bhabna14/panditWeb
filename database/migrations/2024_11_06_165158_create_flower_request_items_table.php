<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flower_request_items', function (Blueprint $table) {
            $table->id();
            $table->string('flower_request_id');
            $table->string('flower_name');
            $table->string('flower_unit');
            $table->integer('flower_quantity');
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
        Schema::dropIfExists('flower_request_items');
    }
};
