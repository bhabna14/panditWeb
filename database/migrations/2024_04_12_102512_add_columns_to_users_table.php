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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('phonenumber')->after('email')->nullable();
            $table->string('dob')->after('phonenumber')->nullable();
            $table->string('bloodgrp')->after('role')->nullable();
            $table->string('qualification')->after('bloodgrp')->nullable();
            $table->string('userphoto')->after('qualification')->nullable();
            $table->string('fathername')->after('userphoto')->nullable();
            $table->string('mothername')->after('fathername')->nullable();
            $table->string('marital')->after('mothername')->nullable();
            $table->string('spouse')->after('marital')->nullable();
            $table->string('datejoin')->after('spouse')->nullable();
            $table->string('seba')->after('datejoin')->nullable();
            $table->string('templeid')->after('seba')->nullable();
            $table->string('bedhaseba')->after('templeid')->nullable();
            $table->string('status')->after('bedhaseba')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
