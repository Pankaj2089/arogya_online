<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpdRegistrationTable extends Migration
{
    public function up()
    {
        Schema::create('ipd_registration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_registration_id')->constrained('opd_registration')->onDelete('cascade');
            $table->date('date');
            $table->string('time', 20)->nullable();
            $table->string('fath_husb_name', 255)->nullable();
            $table->text('address')->nullable();
            $table->text('diagnosis')->nullable();
            $table->foreignId('bed_distribution_id')->nullable()->constrained('bed_distributions')->onDelete('set null');
            $table->unsignedBigInteger('admit_by_user_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->timestamp('created_date')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ipd_registration');
    }
}
