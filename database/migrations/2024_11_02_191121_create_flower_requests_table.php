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
        Schema::create('flower_requests', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('product_id'); // The ID of the product requested
            $table->string('user_id'); // Foreign key for user
            $table->string('address_id'); // Foreign key for address
            $table->text('description'); // Description of the request
            $table->text('suggestion')->nullable(); // Suggestions from the user
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flower_requests');
    }
};
