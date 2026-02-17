<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietPlansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id(); // Auto increment ID
            
            $table->string('ipd_no')->nullable();
            $table->string('opd_no')->nullable();
            $table->string('patient_name');
            $table->string('gendar'); // keeping as requested
            $table->integer('dept_id');

            $table->enum('morning', ['Yes', 'No'])->default('No');
            $table->enum('afternoon', ['Yes', 'No'])->default('No');
            $table->enum('evening', ['Yes', 'No'])->default('No');

            $table->date('plan_date');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};

