<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Rimba\Can\Data\PermissionDefinition;

#[Description('Preview permissions discovered from the application and Rimba packages.')]
#[Signature('rimba:lock-scan
    {--type= : Only show permissions of a specific type}
    {--package= : Only show permissions belonging to a package}
    {--json : Output the discovered permissions as JSON}
')]
final class ScanPermissionsCommand extends BolehCommand
{
    public function handle(): int
    {
        $definitions = $this->definitions();

        if (! $this->ensureDefinitionsWereDiscovered($definitions)) {
            return self::FAILURE;
        }

        $definitions = $this->applyFilters($definitions);

        if ($definitions === []) {
            $this->components->warn(
                'Permissions were discovered, but none matched the supplied filters.',
            );

            return self::SUCCESS;
        }

        if ((bool) $this->option('json')) {

            $this->line(
                (string) json_encode(
                    array_map(
                        fn (
                            PermissionDefinition $definition,
                        ): array => [
                            'name' => $definition->name,
                            'guard_name' => $this->guardName(),
                            'type' => $definition->type,
                            'domain' => $definition->domain,
                            'package' => $definition->package,
                            'resource' => $definition->resource,
                            'action' => $definition->action,
                            'source_class' => $definition->class,
                            'description' => $definition->description,
                        ],
                        $definitions,
                    ),
                    JSON_PRETTY_PRINT
                        | JSON_UNESCAPED_SLASHES,
                ),
            );

            return self::SUCCESS;
        }

        $this->table(
            [
                'Permission',
                'Guard',
                'Type',
                'Domain',
                'Package',
                'Resource',
                'Action',
                'Source Class',
                'Description',
            ],
            array_map(
                fn (
                    PermissionDefinition $definition,
                ): array => [
                    $definition->name,
                    $this->guardName(),
                    $definition->type,
                    $definition->domain ?? '',
                    $this->sourceLabel($definition),
                    $definition->resource ?? '',
                    $definition->action ?? '',
                    $definition->class ?? '',
                    $definition->description ?? '',
                ],
                $definitions,
            ),
        );

        $this->newLine();

        $this->components->info(sprintf(
            'Discovered %d permission(s) for guard [%s].',
            count($definitions),
            $this->guardName(),
        ));

        return self::SUCCESS;
    }

    /**
     * @param  array<int, PermissionDefinition>  $definitions
     * @return array<int, PermissionDefinition>
     */
    private function applyFilters(array $definitions): array
    {
        $type = $this->option('type');
        $package = $this->option('package');

        return array_values(array_filter(
            $definitions,
            static function (
                PermissionDefinition $definition,
            ) use ($type, $package): bool {
                if (
                    is_string($type)
                    && $type !== ''
                    && $definition->type !== $type
                ) {
                    return false;
                }

                if (
                    is_string($package)
                    && $package !== ''
                    && ($definition->package ?? 'app') !== $package
                ) {
                    return false;
                }

                return true;
            },
        ));
    }
}
