<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMagtuUsersTable
 */
final class CreateMagtuUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('magtu')->create('users', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->smallInteger('group_id')->nullable();
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
        Schema::connection('magtu')->drop('users');
    }
}
