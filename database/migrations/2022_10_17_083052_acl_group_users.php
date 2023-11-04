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
            ->create(config('acl.database.prefix') . '_group_users', function (Blueprint $table) {

                /** @var \Rexpl\LaravelAcl\Contracts\PrimaryKeyContract $primaryKeyConfigurator */
                $primaryKeyConfigurator = app(PrimaryKeyContract::class);

                $primaryKeyConfigurator->migrateForeignKey($table, 'user_id');
                $primaryKeyConfigurator->migrateForeignKey($table, 'group_id');

                $table->primary(['user_id', 'group_id']);

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
            ->dropIfExists(config('acl.database.prefix') . '_group_users');
    }
};
