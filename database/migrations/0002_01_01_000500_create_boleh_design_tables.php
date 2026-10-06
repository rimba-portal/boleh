<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('ab_definitions', function (Blueprint $table): void {
        //     $table->id();
        //     $table->string('table_name', 100);
        //     $table->string('key', 120);
        //     $table->string('label');
        //     $table->string('data_type', 30)->default('string');
        //     $table->string('source_model')->nullable();
        //     $table->string('source_relation')->nullable();
        //     $table->string('source_key_column')->default('key');
        //     $table->string('source_value_column')->default('value');
        //     $table->json('allowed_values')->nullable();
        //     $table->boolean('is_multiple')->default(false);
        //     $table->boolean('is_active')->default(true);
        //     $table->text('description')->nullable();
        //     $table->json('meta')->nullable();
        //     $table->timestamps();
        //     $table->unique(['table_name', 'key']);
        // });

        // Schema::create('role_definitions', function (Blueprint $table): void {
        //     $table->id();
        //     $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
        //     $table->string('name');
        //     $table->string('slug')->unique();
        //     $table->string('type', 30)->default('rbac'); // rbac|abac|hybrid|generated|system
        //     $table->string('guard_name', 40)->default('web');
        //     $table->text('description')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->boolean('is_generated')->default(false);
        //     $table->string('generator')->nullable();
        //     $table->json('generator_config')->nullable();
        //     $table->json('meta')->nullable();
        //     $table->timestamps();
        //     $table->index(['type', 'is_active']);
        // });

        // Schema::create('role_definition_permissions', function (Blueprint $table): void {
        //     $table->id();
        //     $table->foreignId('role_definition_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
        //     $table->string('effect', 10)->default('allow');
        //     $table->timestamps();
        //     $table->unique(['role_definition_id', 'permission_id', 'effect'], 'can_role_permission_unique');
        // });

        // Schema::create('condition_groups', function (Blueprint $table): void {
        //     $table->id();
        //     $table->foreignId('role_definition_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('parent_id')->nullable()->constrained('condition_groups')->cascadeOnDelete();
        //     $table->string('boolean_operator', 10)->default('and');
        //     $table->unsignedInteger('sort')->default(0);
        //     $table->timestamps();
        // });

        // Schema::create('condition_rules', function (Blueprint $table): void {
        //     $table->id();
        //     $table->foreignId('condition_group_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('ab_definition_id')->nullable()->constrained()->nullOnDelete();
        //     $table->string('field');
        //     $table->string('operator', 30)->default('=');
        //     $table->json('value')->nullable();
        //     $table->string('value_source', 20)->default('literal'); // literal|attribute|resource
        //     $table->unsignedInteger('sort')->default(0);
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('condition_rules');
        Schema::dropIfExists('condition_groups');
        Schema::dropIfExists('role_definition_permissions');
        Schema::dropIfExists('role_definitions');
        Schema::dropIfExists('ab_definitions');
    }
};
