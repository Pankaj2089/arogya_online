<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegisterTypeToOpdRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opd_registration', function (Blueprint $table) {
            $table->string('register_type', 50)->default('New')->after('dept_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opd_registration', function (Blueprint $table) {
            $table->dropColumn('register_type');
        });
    }
}
