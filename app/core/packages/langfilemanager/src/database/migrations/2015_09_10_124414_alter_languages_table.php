<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('languages', function ($table) {
            $table->string('script', 20)->nullable()->after('abbr');
            $table->string('native', 20)->nullable()->after('script');
        });

        if (Schema::hasTable('languages')) {
            DB::table('languages')->insert([
                'name'    => 'English',
                'flag'    => '',
                'abbr'    => 'en',
                'script'  => 'Latn',
                'native'  => 'English',
                'active'  => '1',
                'default' => '1',
            ]);

            DB::table('languages')->insert([
                'name'    => 'Vietnamese',
                'flag'    => '',
                'abbr'    => 'vi',
                'script'  => 'Latn',
                'native'  => 'Viet Nam',
                'active'  => '1',
                'default' => '0',
            ]);

            DB::table('languages')->insert([
                'name'    => 'Romanian',
                'flag'    => '',
                'abbr'    => 'ro',
                'script'  => 'Latn',
                'native'  => 'română',
                'active'  => '1',
                'default' => '0',
            ]);

            DB::table('languages')->insert([
                'name'    => 'French',
                'flag'    => '',
                'abbr'    => 'fr',
                'script'  => 'Latn',
                'native'  => 'français',
                'active'  => '0',
                'default' => '0',
            ]);

            DB::table('languages')->insert([
                'name'    => 'Italian',
                'flag'    => '',
                'abbr'    => 'it',
                'script'  => 'Latn',
                'native'  => 'italiano',
                'active'  => '0',
                'default' => '0',
            ]);

            DB::table('languages')->insert([
                'name'    => 'Spanish',
                'flag'    => '',
                'abbr'    => 'es',
                'script'  => 'Latn',
                'native'  => 'español',
                'active'  => '0',
                'default' => '0',
            ]);

            DB::table('languages')->insert([
                'name'    => 'German',
                'flag'    => '',
                'abbr'    => 'de',
                'script'  => 'Latn',
                'native'  => 'Deutsch',
                'active'  => '0',
                'default' => '0',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('languages', function ($table) {
            $table->dropColumn('script');
            $table->dropColumn('native');
        });
    }
}
