<?php

declare(strict_types=1);

namespace Rimba\Can\Models;

use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Rimba\Can\Enums\AttributeDataType;

#[Table('ab_definitions')]
#[Appends([
    'code',
])]
#[Unguarded]
final class AttributeDefinition extends Model
{
    protected function casts(): array
    {
        return [
            'data_type' => AttributeDataType::class,
            'allowed_values' => 'array',
            'is_multiple' => 'boolean',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    protected function code(): Attribute
    {
        return Attribute::make(get: function (): string {
            return $this->table_name.'.'.$this->key;
        });
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function forTable(
        Builder $query,
        string $tableName,
    ): Builder {
        return $query->where('table_name', $tableName);
    }
}
