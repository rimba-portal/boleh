<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

final class PermissionDiscoveryService
{
    /** @return list<array{name:string,source:string,subject:string,panel:string}> */
    public function discover(?string $onlyPanel = null): array
    {
        $items = collect();
        foreach (Filament::getPanels() as $panel) {
            $panelId = $panel->getId();
            if ($onlyPanel !== null && $panelId !== $onlyPanel) {
                continue;
            }

            if (config('bites_auth.discovery.panels', true)) {
                $items->push($this->definition(sprintf(config('bites_auth.panels.permission_pattern'), Str::snake($panelId)), 'panel', $panelId, $panelId));
            }

            if (config('bites_auth.discovery.resources', true)) {
                foreach ($panel->getResources() as $resource) {
                    $subject = $this->subject($resource, 'Resource');
                    foreach (config('bites_auth.discovery.resource_permissions', []) as $ability) {
                        $items->push($this->definition($ability.'_'.$subject, 'resource', $resource, $panelId));
                    }
                }
            }

            if (config('bites_auth.discovery.pages', true)) {
                foreach ($panel->getPages() as $page) {
                    $subject = $this->subject($page, 'Page');
                    $items->push($this->definition('view_'.$subject, 'page', $page, $panelId));
                }
            }

            if (config('bites_auth.discovery.widgets', false)) {
                foreach ($panel->getWidgets() as $widget) {
                    $subject = $this->subject($widget, 'Widget');
                    $items->push($this->definition('view_'.$subject.'_widget', 'widget', $widget, $panelId));
                }
            }
        }

        return $items->unique('name')->sortBy('name')->values()->all();
    }

    private function subject(string $class, string $suffix): string
    {
        return Str::snake(Str::beforeLast(class_basename($class), $suffix));
    }

    private function definition(string $name, string $source, string $subject, string $panel): array
    {
        return ['name' => $name, 'source' => $source, 'subject' => $subject, 'panel' => $panel];
    }
}
