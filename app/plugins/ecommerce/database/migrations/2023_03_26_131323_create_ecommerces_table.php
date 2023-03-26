<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec_products', function (Blueprint $table) {
            $table->increments('id');
            $table->json('name');
//            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->json('details')->nullable();
            $table->json('features')->nullable();
            $table->integer('price');
            $table->integer('category_id');
            $table->json('extras')->nullable();
            $table->string('status')->default('out-of-stock');
            $table->string('condition')->default('NEW');
            $table->timestamps();
        });

        Schema::create('ec_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('parent_id')->default(0)->nullable();
            $table->string('slug')->unique();
            $table->integer('lft')->unsigned()->nullable();
            $table->integer('rgt')->unsigned()->nullable();
            $table->integer('depth')->unsigned()->nullable();
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
        Schema::dropIfExists('ec_products');
        Schema::dropIfExists('ec_categories');
    }
};
