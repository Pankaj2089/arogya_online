<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDischargeFieldsToIpdRegistrationTable extends Migration
{
    public function up()
    {
        Schema::table('ipd_registration', function (Blueprint $table) {
            $table->date('discharge_date')->nullable()->after('amount');
            $table->unsignedBigInteger('discharge_dept_id')->nullable()->after('discharge_date');
        });
    }

    public function down()
    {
        Schema::table('ipd_registration', function (Blueprint $table) {
            $table->dropColumn(['discharge_date', 'discharge_dept_id']);
        });
    }
}
