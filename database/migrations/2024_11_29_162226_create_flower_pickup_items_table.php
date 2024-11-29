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
        Schema::create('flower__pickup_items', function (Blueprint $table) {
            $table->id();
            $table->string('pick_up_id');
            $table->string('flower_id');
            $table->string('unit_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2)->nullable(); // Make price nullable
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
        Schema::dropIfExists('flower_pickup_items');
    }
};
