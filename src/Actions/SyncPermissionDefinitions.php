<?php

declare(strict_types=1);

namespace Rimba\Can\Actions;

use Illuminate\Support\Collection;
use Spatie\Permission\PermissionRegistrar;

final class SyncPermissionDefinitions
{
    /** @param iterable<array{name:string,source?:string,subject?:string}> $definitions */
    public function handle(iterable $definitions, bool $prune = false): array
    {
        $model = config('bites_auth.models.permission');
        $guard = (string) config('bites_auth.guard', 'web');
        $names = Collection::make($definitions)->pluck('name')->filter()->unique()->values();

        foreach ($names as $name) {
            $model::findOrCreate($name, $guard);
        }

        if ($prune && (bool) config('bites_auth.sync.prune_generated_permissions', false)) {
            $model::query()->where('guard_name', $guard)->whereNotIn('name', $names)->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $names->all();
    }
}
