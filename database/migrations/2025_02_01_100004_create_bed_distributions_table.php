<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBedDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bed_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->integer('bed_no');
            $table->enum('bed_status', ['available', 'occupied', 'reserved', 'maintenance']);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('bed_distributions');
    }
}
