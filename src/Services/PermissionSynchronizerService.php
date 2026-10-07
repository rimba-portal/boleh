<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Support\Facades\DB;
use Rimba\Can\Contracts\PermissionSynchronizer;
use Rimba\Can\Data\PermissionDefinition;
use Spatie\Permission\Models\Permission;

final class PermissionSynchronizerService implements PermissionSynchronizer
{
    public function sync(
        array $definitions,
        bool $prune = false,
    ): array {
        $guard = (string) config('bites.can.sync.guard', 'web');

        $result = [
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
            'total' => count($definitions),
        ];

        DB::transaction(function () use (
            $definitions,
            $prune,
            $guard,
            &$result,
        ): void {
            $names = [];

            foreach ($definitions as $definition) {
                if (! $definition instanceof PermissionDefinition) {
                    continue;
                }

                $names[] = $definition->name;

                $permission = Permission::query()->firstOrNew([
                    'name' => $definition->name,
                    'guard_name' => $guard,
                ]);

                $wasExisting = $permission->exists;

                $permission->forceFill([
                    'type' => $definition->type,
                    'package' => $definition->package,
                    'resource' => $definition->resource,
                    'action' => $definition->action,
                    'source_class' => $definition->class,
                    'description' => $definition->description,
                ]);

                if (! $wasExisting) {
                    $permission->save();
                    $result['created']++;

                    continue;
                }

                if ($permission->isDirty()) {
                    $permission->save();
                    $result['updated']++;
                }
            }

            if (! $prune) {
                return;
            }

            /*
             * Only prune permissions managed by Boleh.
             *
             * A Boleh permission has discovery metadata. This protects
             * manually created Spatie permissions from accidental deletion.
             */
            $query = Permission::query()
                ->where('guard_name', $guard)
                ->where(function ($query): void {
                    $query
                        ->whereNotNull('type')
                        ->orWhereNotNull('package')
                        ->orWhereNotNull('source_class');
                });

            if ($names === []) {
                return;
            }

            $result['deleted'] = $query
                ->whereNotIn('name', array_values(array_unique($names)))
                ->delete();
        });

        return $result;
    }
}
