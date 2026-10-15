<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Filament\Pages\Page;
use Filament\Resources\Pages\Page as ResourcePage;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use ReflectionClass;
use Rimba\Can\Attributes\Permission;
use Rimba\Can\Attributes\PermissionResource;
use Rimba\Can\Data\PermissionDefinition;
use Rimba\Can\Support\PermissionDescription;
use Rimba\Can\Support\PermissionName;
use Rimba\Can\Support\RimbaSourceScanner;

final class PermissionDiscoveryService
{
    public function __construct(
        private readonly RimbaSourceScanner $rimbaSourceScanner,
    ) {}

    /**
     * @return array<int, PermissionDefinition>
     */
    public function discover(): array
    {
        $found = [];

        foreach ($this->rimbaSourceScanner->scan() as $item) {
            $class = $item['class'];

            if (! class_exists($class) || $this->ignored($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if ($reflection->isAbstract()) {
                continue;
            }

            $this->discoverExplicitPermissions(
                reflection: $reflection,
                class: $class,
                package: $item['package'],
                found: $found,
            );

            if ($reflection->isSubclassOf(Resource::class)) {
                $this->discoverResourcePermissions(
                    reflection: $reflection,
                    class: $class,
                    package: $item['package'],
                    found: $found,
                );

                continue;
            }

            // A Filament Resource page is authorized by its parent Resource.
            // Do not create a second page-level permission for it.
            if ($reflection->isSubclassOf(ResourcePage::class)) {
                continue;
            }

            if ($reflection->isSubclassOf(Page::class)) {
                $this->discoverStandalonePagePermission(
                    class: $class,
                    package: $item['package'],
                    found: $found,
                );
            }
        }

        $unique = [];

        foreach ($found as $permission) {
            $unique[$permission->name] = $permission;
        }

        ksort($unique);

        return array_values($unique);
    }

    /**
     * @param  array<int, PermissionDefinition>  $found
     */
    private function discoverExplicitPermissions(
        ReflectionClass $reflection,
        string $class,
        ?string $package,
        array &$found,
    ): void {
        foreach ($reflection->getAttributes(Permission::class) as $attribute) {
            $permission = $attribute->newInstance();

            $found[] = new PermissionDefinition(
                name: $permission->name,
                type: $permission->type,
                domain: PermissionName::namespacePrefix($class),
                package: $package,
                class: $class,
                description: $permission->description,
            );
        }
    }

    /**
     * @param  array<int, PermissionDefinition>  $found
     */
    private function discoverResourcePermissions(
        ReflectionClass $reflection,
        string $class,
        ?string $package,
        array &$found,
    ): void {
        $resource = null;
        $description = null;

        foreach ($reflection->getAttributes(PermissionResource::class) as $attribute) {
            $permission = $attribute->newInstance();
            $resource = $permission->name;
            $description = $permission->description;
        }

        $resource ??= PermissionName::classToResource($class);

        $domain = PermissionName::namespacePrefix($class);
        $resourceName = Str::after($resource, '.');

        foreach ($this->resourceAbilities() as $ability) {
            $found[] = new PermissionDefinition(
                name: PermissionName::resource($resource, $ability),
                type: 'resource',
                domain: $domain,
                package: $package,
                resource: $resourceName,
                action: $ability,
                class: $class,
                description: $description
                    ?? PermissionDescription::make(
                        resource: $resourceName,
                        ability: $ability,
                        type: 'resource',
                    ),
            );
        }
    }

    /**
     * @param  array<int, PermissionDefinition>  $found
     */
    private function discoverStandalonePagePermission(
        string $class,
        ?string $package,
        array &$found,
    ): void {
        $page = PermissionName::classToPage($class);
        $pageName = Str::after($page, '.');

        $found[] = new PermissionDefinition(
            name: PermissionName::page($page),
            type: 'page',
            domain: PermissionName::namespacePrefix($class),
            package: $package,
            resource: $pageName,
            action: 'access',
            class: $class,
            description: PermissionDescription::make(
                resource: $pageName,
                ability: 'access',
                type: 'page',
            ),
        );
    }

    /**
     * @return array<int, string>
     */
    private function resourceAbilities(): array
    {
        return array_values(array_filter(
            config(
                'bites.can.discovery.resource_crud',
                [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'delete',
                    'restore',
                    'forceDelete',
                ],
            ),
            static fn (mixed $ability): bool => is_string($ability) && trim($ability) !== '',
        ));
    }

    private function ignored(string $class): bool
    {
        foreach (
            config('bites.can.discovery.ignore_namespaces', []) as $namespace
        ) {
            if (Str::startsWith($class, $namespace)) {
                return true;
            }
        }

        return false;
    }
}
