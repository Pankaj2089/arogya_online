<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatientDetailsToIpdRegistrationTable extends Migration
{
    public function up()
    {
        Schema::table('ipd_registration', function (Blueprint $table) {
            $table->string('patient_name', 255)->nullable()->after('opd_registration_id');
            $table->unsignedInteger('patient_age')->nullable()->after('patient_name');
            $table->string('patient_age_unit', 20)->nullable()->after('patient_age');
            $table->string('gender', 50)->nullable()->after('patient_age_unit');
            $table->string('opd_number', 255)->nullable()->after('gender');
            $table->string('hid_number', 255)->nullable()->after('opd_number');
            $table->unsignedBigInteger('dept_id')->nullable()->after('hid_number');
            $table->string('category', 100)->nullable()->after('dept_id');
        });
    }

    public function down()
    {
        Schema::table('ipd_registration', function (Blueprint $table) {
            $table->dropColumn([
                'patient_name', 'patient_age', 'patient_age_unit', 'gender',
                'opd_number', 'hid_number', 'dept_id', 'category'
            ]);
        });
    }
}
