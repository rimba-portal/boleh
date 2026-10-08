<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Filament\Pages\Page;
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

            /*
             |--------------------------------------------------------------
             | Explicit Permissions
             |--------------------------------------------------------------
             */

            foreach (
                $reflection->getAttributes(Permission::class) as $attribute
            ) {
                $permission = $attribute->newInstance();

                $found[] = new PermissionDefinition(
                    name: $permission->name,
                    type: $permission->type,
                    domain: PermissionName::namespacePrefix($class),
                    package: $item['package'],
                    class: $class,
                    description: $permission->description,
                );
            }

            /*
             |--------------------------------------------------------------
             | Filament Resources
             |--------------------------------------------------------------
             */

            if ($reflection->isSubclassOf(Resource::class)) {

                $resource = null;
                $description = null;

                foreach (
                    $reflection->getAttributes(
                        PermissionResource::class,
                    ) as $attribute
                ) {
                    $permission = $attribute->newInstance();

                    $resource = $permission->name;
                    $description = $permission->description;
                }

                $resource ??= PermissionName::classToResource(
                    $class,
                );

                $domain = PermissionName::namespacePrefix(
                    $class,
                );

                foreach (
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
                    ) as $ability
                ) {
                    $resourceName = Str::after(
                        $resource,
                        '.',
                    );

                    $found[] = new PermissionDefinition(
                        name: PermissionName::resource(
                            $resource,
                            $ability,
                        ),
                        type: 'resource',
                        domain: $domain,
                        package: $item['package'],
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

                continue;
            }

            /*
             |--------------------------------------------------------------
             | Filament Pages
             |--------------------------------------------------------------
             */

            if ($reflection->isSubclassOf(Page::class)) {

                $page = PermissionName::classToPage(
                    $class,
                );

                $pageName = Str::after(
                    $page,
                    '.',
                );

                $found[] = new PermissionDefinition(
                    name: $page,
                    type: 'page',
                    domain: PermissionName::namespacePrefix(
                        $class,
                    ),
                    package: $item['package'],
                    resource: $pageName,
                    class: $class,
                    description: PermissionDescription::make(
                        resource: $pageName,
                        ability: null,
                        type: 'page',
                    ),
                );
            }
        }

        $unique = [];

        foreach ($found as $permission) {
            $unique[$permission->name] = $permission;
        }

        return array_values($unique);
    }

    private function ignored(
        string $class,
    ): bool {
        foreach (
            config(
                'bites.can.discovery.ignore_namespaces',
                [],
            ) as $namespace
        ) {
            if (
                Str::startsWith(
                    $class,
                    $namespace,
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
