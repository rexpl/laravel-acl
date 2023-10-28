<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Rexpl\LaravelAcl\Contracts\PrimaryKeyContract;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('acl.database.connection'))
            ->create(config('acl.database.prefix') . '_groups', function (Blueprint $table) {

                /** @var \Rexpl\LaravelAcl\Contracts\PrimaryKeyContract $primaryKeyConfigurator */
                $primaryKeyConfigurator = app(PrimaryKeyContract::class);

                $primaryKeyConfigurator->migratePrimaryKey($table);
                $table->string('name')->nullable();
                $primaryKeyConfigurator->migrateForeignKey($table, 'user_id')->nullable();

                if (config('acl.database.timestamps')) {
                    $table->timestamps();
                }
            });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('acl.database.connection'))
            ->dropIfExists(config('acl.database.prefix') . '_groups');
    }
};
