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
        Schema::create('variants', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->bigInteger('variant_id')->unique(); // Shopify variant ID
            $table->bigInteger('product_id')->index(); // Foreign key to products table
            $table->string('title'); // Title of the variant
            $table->decimal('price', 8, 2); // Price of the variant
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
        Schema::dropIfExists('variants');
    }
};
