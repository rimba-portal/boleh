<?php

declare(strict_types=1);

namespace Rimba\Can\Models;

use HosseinHezami\PermissionManager\Models\Permission;
use HosseinHezami\PermissionManager\Models\Role;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Can\Enums\RoleType;

#[Unguarded]
final class RoleDefinition extends Model
{
    protected function casts(): array
    {
        return [
            'type' => RoleType::class,
            'is_active' => 'boolean',
            'is_generated' => 'boolean',
            'generator_config' => 'array',
            'meta' => 'array',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Permission::class,
            table: 'role_definition_permissions',
        )
            ->withPivot('effect')
            ->withTimestamps();
    }

    public function allowedPermissions(): BelongsToMany
    {
        return $this->permissions()
            ->wherePivot('effect', 'allow');
    }

    public function deniedPermissions(): BelongsToMany
    {
        return $this->permissions()
            ->wherePivot('effect', 'deny');
    }

    public function conditionGroups(): HasMany
    {
        return $this->hasMany(ConditionGroup::class)
            ->orderBy('sort');
    }

    public function rootConditionGroups(): HasMany
    {
        return $this->conditionGroups()
            ->whereNull('parent_id');
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function ofType(
        Builder $query,
        RoleType|string $type,
    ): Builder {
        $value = $type instanceof RoleType
            ? $type->value
            : $type;

        return $query->where('type', $value);
    }

    public function isCompiled(): bool
    {
        return $this->role_id !== null;
    }

    public function requiresConditions(): bool
    {
        return $this->type?->requiresConditions() ?? false;
    }
}
