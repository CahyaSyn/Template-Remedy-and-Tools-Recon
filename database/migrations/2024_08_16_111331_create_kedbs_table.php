<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKedbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kedbs', function (Blueprint $table) {
            $table->id('kedb_id');
            $table->integer('kedb_parent_id')->nullable();
            $table->integer('kedb_child_id')->nullable();
            $table->string('app_id')->nullable();
            $table->string('old_kedb')->nullable();
            $table->string('new_symtom_kedb')->nullable();
            $table->string('new_specific_symtom_kedb')->nullable();
            $table->string('kedb_finalisasi')->nullable();
            $table->string('action')->nullable();
            $table->string('responsibility_action')->nullable();
            $table->string('sop')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kedbs');
    }
}
