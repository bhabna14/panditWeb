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
        Schema::create('addressdetails', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->string('preaddress');
            $table->string('prepost');
            $table->string('predistrict');
            $table->string('prestate');
            $table->string('precountry');
            $table->string('prepincode');
            $table->string('prelandmark');
            $table->string('peraddress');
            $table->string('perpost');
            $table->string('perdistri');
            $table->string('perstate');
            $table->string('percountry');
            $table->string('perpincode');
            $table->string('perlandmark');
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
        Schema::dropIfExists('addressdetails');
    }
};
