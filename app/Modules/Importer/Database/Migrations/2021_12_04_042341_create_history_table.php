<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->string('action_type')->nullable();
            $table->string('changes')->nullable();
            $table->string('columnname')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->integer('person_id')->nullable();
            $table->integer('record_id')->nullable();
            $table->integer('related_record_id')->nullable();
            $table->string('related_tablename')->nullable();
            $table->string('tablename')->nullable();
            $table->string('value_from')->nullable();
            $table->string('value_to')->nullable();
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history');
    }
};