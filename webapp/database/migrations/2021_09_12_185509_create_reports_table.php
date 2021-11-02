<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('vehicle_code')->nullable();
            $table->string('other_make_model')->nullable();
            $table->string('street_address_1');
            $table->string('street_address_2');
            $table->string('zip');
            $table->string('police_report_number')->default("");
            $table->text('description')->default("");
            $table->boolean('admin_approved')->default(false);
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
        Schema::dropIfExists('reports');
    }
}
