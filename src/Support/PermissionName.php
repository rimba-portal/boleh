<?php

declare(strict_types=1);

namespace Rimba\Can\Support;

use Illuminate\Support\Str;

final class PermissionName
{
    public static function namespacePrefix(string $class): string
    {
        $parts = explode('\\', trim($class, '\\'));

        return Str::of($parts[1] ?? 'app')
            ->snake()
            ->replace('_', '-')
            ->lower()
            ->toString();
    }

    public static function resource(
        string $domain,
        string $resource,
        string $action,
    ): string {
        return "{$action}.{$domain}.{$resource}";
    }

    public static function page(
        string $domain,
        string $resource,
        string $action,
    ): string {
        return "{$action}.{$domain}.{$resource}";
    }

    public static function classToResource(string $class): string
    {
        return self::className($class, 'Resource');
    }

    public static function classToPage(string $class): string
    {
        return self::className($class, 'Page');
    }

    private static function className(
        string $class,
        string $suffix,
    ): string {
        $name = preg_replace(
            '/'.preg_quote($suffix, '/').'$/',
            '',
            class_basename($class),
        );

        return Str::of($name)
            ->snake()
            ->replace('_', '-')
            ->lower()
            ->toString();
    }
}
