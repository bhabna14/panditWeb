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
        Schema::create('flower__pickup_details', function (Blueprint $table) {
            $table->id();
            $table->string('flower_id'); // Reference to flower_product table
            $table->string('unit_id');   // Reference to unit table
            $table->decimal('quantity', 10, 2);      // Quantity of the flower
            $table->string('vendor_id'); // Reference to vendor_details table
            $table->string('rider_id');  // Reference to rider_details table
            $table->date('pickup_date');             // Date of pickup
            $table->decimal('price', 10, 2)->nullable(); // Price added by the rider
            $table->string('status'); // Status of pickup
            $table->timestamps();                    // Created and updated timestamps
        
            // Add foreign keys if required
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flower__pickup_details', function (Blueprint $table) {
            //
        });
    }
};
