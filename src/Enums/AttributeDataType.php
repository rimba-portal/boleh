<?php

declare(strict_types=1);

namespace Rimba\Can\Enums;

enum AttributeDataType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Decimal = 'decimal';
    case Boolean = 'boolean';
    case Date = 'date';
    case Enum = 'enum';
    case Uuid = 'uuid';

    public function label(): string
    {
        return match ($this) {
            self::String => 'String',
            self::Integer => 'Integer',
            self::Decimal => 'Decimal',
            self::Boolean => 'Boolean',
            self::Date => 'Date',
            self::Enum => 'Enum',
            self::Uuid => 'UUID',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(
                fn (self $type): array => [
                    $type->value => $type->label(),
                ],
            )
            ->all();
    }
}
