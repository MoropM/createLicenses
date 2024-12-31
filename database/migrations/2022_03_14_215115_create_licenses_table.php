<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('uri_access', 80)->unique();
            $table->string('access_token', 150)->nullable();
            $table->string('start_date', 10);
            $table->string('finish_date', 10);
            $table->string('status', 15)->nullable();
            $table->mediumText('license_token')->nullable();
            $table->string('license', 25)->nullable();
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
        Schema::dropIfExists('licenses');
    }
}
