<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained();
            $table->foreignId('role_id')->constrained();
            $table->foreignId('org_corp_id')->nullable()->constrained();
            $table->foreignId('org_unit_id')->nullable()->constrained();
            $table->foreignId('org_team_id')->nullable()->constrained();
            $table->foreignId('job_position_id')->nullable()->constrained();
            $table->foreignId('job_role_id')->nullable()->constrained();
            $table->foreignId('job_contract_id')->nullable()->constrained();
            $table->enum('source', ['auto', 'manual'])->default('auto');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
        Schema::create('role_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained();
            $table->foreignId('staff_id')->constrained();
            $table->foreignId('assigned_by')->nullable()->constrained('staff');
            $table->foreignId('org_unit_id')->nullable()->constrained();
            $table->foreignId('org_team_id')->nullable()->constrained();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_assignments');
        Schema::dropIfExists('staff_roles');
    }
};
