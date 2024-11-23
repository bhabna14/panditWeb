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
        Schema::create('flower_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('payment_id')->nullable();
            $table->string('user_id');
            $table->string('payment_method')->nullable();
            $table->decimal('paid_amount', 10, 2);
            $table->string('payment_status')->default('pending');
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
        Schema::dropIfExists('flower_payments');
    }
};
