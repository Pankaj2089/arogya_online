<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeBedNoToVarcharInBedDistributions extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE bed_distributions MODIFY bed_no VARCHAR(50) NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE bed_distributions MODIFY bed_no INT NOT NULL');
    }
}
