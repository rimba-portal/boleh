<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Collection;
use Rimba\Can\Data\PermissionDefinition;
use Spatie\Permission\Models\Permission;

#[Description('Check discovered permissions against the permission database.')]
#[Signature('rimba:lock-check
    {--no-orphans : Do not fail when orphaned permissions are found}
')]
final class CheckPermissionsCommand extends BolehCommand
{
    public function handle(): int
    {
        $definitions = $this->definitions();

        if (! $this->ensureDefinitionsWereDiscovered($definitions)) {
            return self::FAILURE;
        }

        $guard = $this->guardName();

        /** @var Collection<string, Permission> $existing */
        $existing = Permission::query()
            ->where('guard_name', $guard)
            ->orderBy('name')
            ->get()
            ->keyBy('name');

        /** @var array<string, PermissionDefinition> $discovered */
        $discovered = [];

        foreach ($definitions as $definition) {
            $discovered[$definition->name] = $definition;
        }

        $missing = [];
        $outdated = [];

        foreach ($discovered as $name => $definition) {
            $permission = $existing->get($name);

            if ($permission === null) {
                $missing[] = $definition;

                continue;
            }

            $differences = $this->metadataDifferences(
                $permission,
                $definition,
            );

            if ($differences !== []) {
                $outdated[] = [
                    'name' => $name,
                    'fields' => implode(', ', $differences),
                ];
            }
        }

        $orphaned = $existing
            ->keys()
            ->diff(array_keys($discovered))
            ->values()
            ->all();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Discovered', count($discovered)],
                ['Existing', $existing->count()],
                ['Missing', count($missing)],
                ['Outdated', count($outdated)],
                ['Orphaned', count($orphaned)],
            ],
        );

        $this->renderMissing($missing);
        $this->renderOutdated($outdated);
        $this->renderOrphaned($orphaned);

        $hasBlockingDifference =
            $missing !== []
            || $outdated !== []
            || (
                ! (bool) $this->option('no-orphans')
                && $orphaned !== []
            );

        if ($hasBlockingDifference) {
            $this->newLine();
            $this->components->error(
                'The permission database does not match discovered permissions.',
            );

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info(
            'The permission database matches discovered permissions.',
        );

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function metadataDifferences(
        Permission $permission,
        PermissionDefinition $definition,
    ): array {
        $expected = [
            'type' => $definition->type,
            'domain' => $definition->domain,
            'package' => $definition->package,
            'resource' => $definition->resource,
            'action' => $definition->action,
            'source_class' => $definition->class,
            'description' => $definition->description,
        ];

        $different = [];

        foreach ($expected as $field => $value) {

            $actual = $permission->getAttribute(
                $field,
            );

            if (
                $this->normalize($actual)
                !==
                $this->normalize($value)
            ) {
                $different[] = $field;
            }
        }

        return $different;
    }

    private function normalize(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<int, PermissionDefinition>  $missing
     */
    private function renderMissing(array $missing): void
    {
        if ($missing === []) {
            return;
        }

        $this->newLine();
        $this->components->warn('Missing permissions');

        $this->table(
            ['Permission', 'Type', 'Package'],
            array_map(
                fn (PermissionDefinition $definition): array => [
                    $definition->name,
                    $definition->type,
                    $this->sourceLabel($definition),
                ],
                $missing,
            ),
        );
    }

    /**
     * @param  array<int, array{name: string, fields: string}>  $outdated
     */
    private function renderOutdated(array $outdated): void
    {
        if ($outdated === []) {
            return;
        }

        $this->newLine();
        $this->components->warn('Permissions with outdated metadata');

        $this->table(
            ['Permission', 'Different fields'],
            array_map(
                static fn (array $item): array => [
                    $item['name'],
                    $item['fields'],
                ],
                $outdated,
            ),
        );
    }

    /**
     * @param  array<int, string>  $orphaned
     */
    private function renderOrphaned(array $orphaned): void
    {
        if ($orphaned === []) {
            return;
        }

        $this->newLine();
        $this->components->warn('Orphaned permissions');

        $this->table(
            ['Permission'],
            array_map(
                static fn (string $permission): array => [$permission],
                $orphaned,
            ),
        );
    }
}
