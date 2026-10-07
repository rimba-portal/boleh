<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Command;
use Rimba\Can\Data\PermissionDefinition;
use Rimba\Can\Services\PermissionDiscoveryService;

abstract class BolehCommand extends Command
{
    /**
     * @return array<int, PermissionDefinition>
     */
    protected function definitions(): array
    {
        $definitions = app(PermissionDiscoveryService::class)->discover();

        $definitions = array_values(array_filter(
            $definitions,
            static fn (mixed $definition): bool => $definition instanceof PermissionDefinition
                && trim($definition->name) !== '',
        ));

        usort(
            $definitions,
            static fn (
                PermissionDefinition $left,
                PermissionDefinition $right,
            ): int => strcmp($left->name, $right->name),
        );

        return $definitions;
    }

    protected function guardName(): string
    {
        return (string) config('bites.can.sync.guard', 'web');
    }

    protected function ensureDefinitionsWereDiscovered(
        array $definitions,
    ): bool {
        if ($definitions !== []) {
            return true;
        }

        $this->components->error(
            'No permissions were discovered. Synchronization was stopped.',
        );

        $this->line('');
        $this->line('Check the following configuration values:');
        $this->line('  bites.can.application.path');
        $this->line('  bites.can.application.namespace');
        $this->line('  bites.can.rimba.path');
        $this->line('  bites.can.discovery.resource_crud');

        return false;
    }

    protected function sourceLabel(
        PermissionDefinition $definition,
    ): string {
        return $definition->package ?: 'app';
    }
}
