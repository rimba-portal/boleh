<?php

declare(strict_types=1);

namespace Rimba\Can\Support;

use Illuminate\Support\Str;

final class PermissionName
{
    public static function namespacePrefix(
        string $class,
    ): string {
        $parts = explode('\\', trim($class, '\\'));

        return Str::of($parts[1] ?? 'app')
            ->snake()
            ->replace('_', '-')
            ->lower()
            ->toString();
    }

    public static function resource(
        string $resource,
        string $ability,
    ): string {
        return "{$resource}.{$ability}";
    }

    public static function classToResource(
        string $class,
    ): string {
        $prefix = self::namespacePrefix($class);

        $name = preg_replace(
            '/Resource$/',
            '',
            class_basename($class),
        );

        $name = Str::of($name)
            ->snake()
            ->replace('_', '-')
            ->lower()
            ->toString();

        return "{$prefix}.{$name}";
    }

    public static function classToPage(
        string $class,
    ): string {
        $prefix = self::namespacePrefix($class);

        $name = preg_replace(
            '/Page$/',
            '',
            class_basename($class),
        );

        $name = Str::of($name)
            ->snake()
            ->replace('_', '-')
            ->lower()
            ->toString();

        return "{$prefix}.{$name}";
    }
}
