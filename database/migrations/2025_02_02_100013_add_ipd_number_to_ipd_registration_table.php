<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpdNumberToIpdRegistrationTable extends Migration
{
    public function up()
    {
        Schema::table('ipd_registration', function (Blueprint $table) {
            $table->string('ipd_number', 50)->nullable()->after('opd_registration_id');
        });
    }

    public function down()
    {
        Schema::table('ipd_registration', function (Blueprint $table) {
            $table->dropColumn('ipd_number');
        });
    }
}
