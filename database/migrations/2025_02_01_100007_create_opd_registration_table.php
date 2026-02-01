<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpdRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opd_registration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_year_id')->constrained('financial_years')->onDelete('cascade');
            $table->string('patient_name');
            $table->string('fath_husb_name')->nullable();
            $table->string('address')->nullable();
            $table->date('date');
            $table->unsignedInteger('patient_age')->default(0);
            $table->string('patient_age_unit', 20)->default('Years');
            $table->string('gender', 20)->nullable();
            $table->foreignId('dept_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('opd_number')->unique();
            $table->string('hid_number')->unique();
            $table->timestamp('created_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opd_registration');
    }
}
