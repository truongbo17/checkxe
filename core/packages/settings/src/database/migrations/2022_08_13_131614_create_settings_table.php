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
            $table->string('name');
            $table->string('key')->unique()->index();
            $table->text('value')->nullable();
            $table->string('type')->index()->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('active')->default(0);
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
