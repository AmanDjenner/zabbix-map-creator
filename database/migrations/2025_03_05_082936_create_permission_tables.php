<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        // Tabel permissions
        if (!Schema::hasTable($tableNames['permissions'])) {
            Schema::create($tableNames['permissions'], function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 100)->nullable(false);
                $table->string('guard_name', 100)->default('web');
                $table->timestamps();
                $table->unique('name', 'permissions_name_unique');
            });
        }

        // Tabel roles
        if (!Schema::hasTable($tableNames['roles'])) {
            Schema::create($tableNames['roles'], function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 100)->nullable(false);
                $table->string('guard_name', 100)->default('web');
                $table->timestamps();
                $table->unique('name', 'roles_name_unique');
            });
        }

        // Tabel model_has_permissions
        if (!Schema::hasTable($tableNames['model_has_permissions'])) {
            Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $pivotPermission) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger($pivotPermission);
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->foreign($pivotPermission)->references('id')->on($tableNames['permissions'])->onDelete('cascade');
                $table->index(['model_id', 'model_type'], 'model_has_permissions_model_idx');
                $table->unique([$pivotPermission, 'model_id', 'model_type'], 'model_has_permissions_unique');
            });
        }

        // Tabel model_has_roles
        if (!Schema::hasTable($tableNames['model_has_roles'])) {
            Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $pivotRole) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger($pivotRole);
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->foreign($pivotRole)->references('id')->on($tableNames['roles'])->onDelete('cascade');
                $table->index(['model_id', 'model_type'], 'model_has_roles_model_idx');
                $table->unique([$pivotRole, 'model_id', 'model_type'], 'model_has_roles_unique');
            });
        }

        // Tabel roles_and_permissions
        if (!Schema::hasTable('roles_and_permissions')) {
            Schema::create('roles_and_permissions', function (Blueprint $table) use ($pivotRole, $pivotPermission) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger($pivotPermission);
                $table->unsignedBigInteger($pivotRole);
                $table->foreign($pivotPermission)->references('id')->on('permissions')->onDelete('cascade');
                $table->foreign($pivotRole)->references('id')->on('roles')->onDelete('cascade');
                $table->unique([$pivotPermission, $pivotRole], 'roles_and_permissions_unique');
            });
        }

        app('cache')->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_and_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};