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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Replace with the actual foreign key constraint names if different
            $table->dropForeign(['product_id']); // Dropping the foreign key for product_id
            $table->dropForeign(['user_id']); // Dropping the foreign key for user_id
        });
    }

   

    /**
     * Reverse the migrations.
     *
     * @return void
     */


    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Re-add the foreign key constraints if you roll back
            $table->string('product_id')->references('id')->on('flower_products')->onDelete('cascade');
            $table->string('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
