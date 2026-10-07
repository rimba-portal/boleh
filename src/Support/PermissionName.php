<?php

declare(strict_types=1);

namespace Rimba\Can\Support;

use Illuminate\Support\Str;

final class PermissionName
{
    public static function resource(string $resource, string $ability): string
    {
        return "$resource.$ability";
    }

    public static function classToResource(string $class, ?string $package = null): string
    {
        $name = preg_replace('/Resource$/', '', class_basename($class));
        $name = Str::of($name)->snake()->replace('_', '-')->lower()->toString();

        return $package ? "$package.$name" : $name;
    }

    public static function classToPage(string $class, ?string $package = null): string
    {
        $name = preg_replace('/Page$/', '', class_basename($class));
        $name = Str::of($name)->snake()->replace('_', '-')->lower()->toString();

        return $package ? "$package.$name" : $name;
    }
}
