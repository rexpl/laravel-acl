<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_group_dependencies', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('model_id');
            $table->foreignId('record_id');
            $table->foreignId('group_id');
            $table->tinyInteger('permission_level');
            $table->index(['model_id', 'record_id']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acl_group_dependencies');
    }
};
