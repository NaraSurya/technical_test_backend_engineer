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
        Schema::create('vacancy', function (Blueprint $table) {
            $table->id('vacancy_id');
            $table->string('vacancy_name');
            $table->smallInteger('min_age');
            $table->smallInteger('max_age');
            $table->enum('requirement_gender', ['Male', 'Female','All']);
            $table->timestamp('created_date');
            $table->date('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacancy');
    }
};
