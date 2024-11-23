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
        Schema::create('subscription_pause_resume_logs', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id'); // Foreign key to subscriptions table
            $table->string('action'); // Action (paused or resumed)
            $table->date('pause_start_date')->nullable(); // Date when paused
            $table->date('pause_end_date')->nullable(); // Date when paused
            $table->date('new_end_date')->nullable(); // New end date after resuming
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
        Schema::dropIfExists('subscription_pause_resume_logs');
    }
};
