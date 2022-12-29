<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('bo.setting.table_name'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique()->index();
            $table->string('name');
            $table->string('description')->nullable();
            $table->text('value')->nullable();
            $table->text('field');
            $table->string('type')->index();
            $table->tinyInteger('active');
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
        Schema::drop(config('bo.setting.table_name'));
    }
}
