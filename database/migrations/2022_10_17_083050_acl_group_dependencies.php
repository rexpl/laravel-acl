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
            $table->string('ressource');
            $table->integer('ressource_id');
            $table->integer('group_id');
            $table->integer('permission_level');
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