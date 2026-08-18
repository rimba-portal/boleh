<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->string('attribute_key');
            $table->string('operator', 40);
            $table->json('value')->nullable();
            $table->string('group')->default('default');
            $table->boolean('required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['permission_id', 'is_active']);
            $table->index(['attribute_key', 'operator']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_rules');
    }
};
