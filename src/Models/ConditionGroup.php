<?php

declare(strict_types=1);

namespace Rimba\Can\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Unguarded]
final class ConditionGroup extends Model
{
    protected function casts(): array
    {
        return [
            'sort' => 'integer',
        ];
    }

    public function roleDefinition(): BelongsTo
    {
        return $this->belongsTo(RoleDefinition::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            related: self::class,
            foreignKey: 'parent_id',
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            related: self::class,
            foreignKey: 'parent_id',
        )->orderBy('sort');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(ConditionRule::class)
            ->orderBy('sort');
    }

    public function activeRules(): HasMany
    {
        return $this->rules()
            ->where('is_active', true);
    }
}
