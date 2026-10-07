<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Support\Facades\DB;
use Rimba\Can\Contracts\PermissionSynchronizer;
use Spatie\Permission\Models\Permission;

final class PermissionSynchronizerService implements PermissionSynchronizer
{
    public function sync(array $definitions, bool $prune = false): array
    {
        $created = 0;
        $updated = 0;
        $deleted = 0;
        $names = [];
        $guard = config('bites.can.sync.guard', 'web');
        DB::transaction(function () use ($definitions, $prune, $guard, &$created, &$updated, &$deleted, &$names): void {
            foreach ($definitions as $definition) {
                $names[] = $definition->name;
                $p = Permission::query()->firstOrNew(['name' => $definition->name, 'guard_name' => $guard]);
                $exists = $p->exists;
                $p->forceFill(['type' => $definition->type, 'package' => $definition->package, 'resource' => $definition->resource, 'action' => $definition->action, 'source_class' => $definition->class, 'description' => $definition->description]);
                $p->save();
                $exists ? $updated++ : $created++;
            } if ($prune) {
                $deleted = Permission::query()->where('guard_name', $guard)->whereNotIn('name', $names)->delete();
            }
        });

        return ['created' => $created, 'updated' => $updated, 'deleted' => $deleted, 'total' => count($definitions)];
    }
}
