<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiseaseIdToOpdRegistrationTable extends Migration
{
    public function up()
    {
        Schema::table('opd_registration', function (Blueprint $table) {
            $table->unsignedBigInteger('disease_id')->nullable()->after('hid_number');
        });
    }

    public function down()
    {
        Schema::table('opd_registration', function (Blueprint $table) {
            $table->dropColumn('disease_id');
        });
    }
}
