<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWorkOrderNumberInWorkOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_order', function (Blueprint $table) {
            $table->string('work_order_number')->unique()->change();
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_order', function (Blueprint $table) {
            $table->string('work_order_number')->dropUnique()->change();
        });
    }
};
