<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Ensure pooja_id is unsignedBigInteger
            $table->unsignedBigInteger('pooja_id')->change();

            // Add the foreign key constraint
            $table->foreign('pooja_id')->references('id')->on('pooja_list')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['pooja_id']);
        });
    }
}
