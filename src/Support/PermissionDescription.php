<?php

declare(strict_types=1);

namespace Rimba\Can\Support;

use Illuminate\Support\Str;

final class PermissionDescription
{
    public static function make(
        string $resource,
        ?string $ability,
        string $type,
    ): string {
        $resource = Str::of($resource)
            ->replace('-', ' ')
            ->headline()
            ->toString();

        $verb = Str::of($ability ?? '')
            ->snake(' ')
            ->headline()
            ->toString();

        return match ($type) {
            'resource' => "Can {$verb} {$resource} Resource",

            'page' => "Can Access {$resource} Page",

            'action' => "Can Execute {$resource} Action",

            'panel' => "Can Access {$resource} Panel",

            default => "Can {$verb} {$resource}",
        };
    }
}
