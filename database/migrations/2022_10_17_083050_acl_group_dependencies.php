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
            $table->foreignId('ressource_id');
            $table->foreignId('group_id');
            $table->tinyInteger('permission_level');
            $table->index(['ressource', 'ressource_id']);
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