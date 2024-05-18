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
        Schema::create('career_names', function (Blueprint $table) {
            $table->id('career_id'); // Auto-incrementing career_id
            $table->string('qualification');
            $table->integer('total_experience');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('career_names');
    }
};
