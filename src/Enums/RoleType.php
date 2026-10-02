<?php

declare(strict_types=1);

namespace Rimba\Can\Enums;

enum RoleType: string
{
    case Rbac = 'rbac';
    case Abac = 'abac';
    case Hybrid = 'hybrid';
    case Generated = 'generated';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Rbac => 'RBAC',
            self::Abac => 'ABAC',
            self::Hybrid => 'Hybrid',
            self::Generated => 'Generated',
            self::System => 'System',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Rbac => 'A role explicitly assigned to users.',
            self::Abac => 'A role dynamically resolved from subject attributes.',
            self::Hybrid => 'A role combining explicit assignment and attribute conditions.',
            self::Generated => 'A role generated from organizational or application data.',
            self::System => 'A system-managed role that should not normally be deleted.',
        };
    }

    public function requiresConditions(): bool
    {
        return in_array($this, [
            self::Abac,
            self::Hybrid,
        ], true);
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
