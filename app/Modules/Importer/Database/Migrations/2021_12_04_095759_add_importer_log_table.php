<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImporterLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importer_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->dateTime('run_at');
            $table->integer('entries_processed');
            $table->integer('entries_created');
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importer_log');
    }
};
