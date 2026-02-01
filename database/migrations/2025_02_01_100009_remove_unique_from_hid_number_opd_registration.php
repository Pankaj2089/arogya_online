<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueFromHidNumberOpdRegistration extends Migration
{
    /**
     * Run the migrations.
     * Re-schedule reuses same hid_number for the same patient; allow duplicate hid_number.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opd_registration', function (Blueprint $table) {
            $table->dropUnique(['hid_number']);
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
            $table->unique('hid_number');
        });
    }
}
