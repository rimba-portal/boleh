<?php

declare(strict_types=1);

namespace Rimba\Can\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rimba\Can\Enums\RuleOperator;
use Spatie\Permission\Models\Permission;

final class PermissionRule extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'operator' => RuleOperator::class,
            'value' => 'array',
            'required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(config('bites_auth.models.permission', Permission::class));
    }
}
