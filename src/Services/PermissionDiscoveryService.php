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
use Rimba\Can\Support\PermissionName;
use Rimba\Can\Support\RimbaSourceScanner;

final class PermissionDiscoveryService
{
    public function __construct(private readonly RimbaSourceScanner $rimbaSourceScanner) {}

    public function discover(): array
    {
        $found = [];
        foreach ($this->rimbaSourceScanner->scan() as $item) {
            $class = $item['class'];
            if (! class_exists($class) || $this->ignored($class)) {
                continue;
            } $r = new ReflectionClass($class);
            foreach ($r->getAttributes(Permission::class) as $a) {
                $p = $a->newInstance();
                $found[] = new PermissionDefinition($p->name, $p->type, $item['package'], class: $class, description: $p->description);
            } if ($r->isSubclassOf(Resource::class)) {
                $name = null;
                $desc = null;
                foreach ($r->getAttributes(PermissionResource::class) as $a) {
                    $p = $a->newInstance();
                    $name = $p->name;
                    $desc = $p->description;
                } $name ??= PermissionName::classToResource($class, $item['package']);
                foreach (config('bites.can.discovery.resource_crud', ['view', 'create', 'edit', 'delete']) as $ability) {
                    $found[] = new PermissionDefinition(PermissionName::resource($name, $ability), 'resource', $item['package'], $name, $ability, $class, $desc);
                }
            } elseif ($r->isSubclassOf(Page::class)) {
                $page = PermissionName::classToPage($class, $item['package']);
                $found[] = new PermissionDefinition($page.'.view', 'page', $item['package'], class: $class);
            }
        } $unique = [];
        foreach ($found as $d) {
            $unique[$d->name] = $d;
        }

return array_values($unique);
    }

    private function ignored(string $class): bool
    {
        foreach (config('bites.can.discovery.ignore_namespaces', []) as $ns) {
            if (Str::startsWith($class, $ns)) {
                return true;
            }
        }

return false;
    }
}
