<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('bo.permissionmanager.table_roles'), function (Blueprint $table) {
            $table->bigIncrements('id'); // role id
            $table->string('name')->index();
            $table->text('list_route_admin');
            $table->timestamps();
            $table->unique(['name']);
        });

        Schema::create(config('bo.permissionmanager.table_model_has_roles'), function (Blueprint $table) {
            $table->unsignedBigInteger(config('bo.permissionmanager.model_has_role_primary_key'));

            $table->string('model_type');
            $table->unsignedBigInteger(config('bo.permissionmanager.model_morph_key'));
            $table->index([config('bo.permissionmanager.model_morph_key'), 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign(config('bo.permissionmanager.model_has_role_primary_key'))
                ->references('id') // role id
                ->on(config('bo.permissionmanager.table_roles'))
                ->onDelete('cascade');

            $table->primary([config('bo.permissionmanager.model_has_role_primary_key'), config('bo.permissionmanager.model_morph_key'), 'model_type'],
                'model_has_roles_role_model_type_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('bo.permissionmanager.table_model_has_roles'));
        Schema::drop(config('bo.permissionmanager.table_roles'));
    }
}
