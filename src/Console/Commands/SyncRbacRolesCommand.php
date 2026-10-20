<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rimba\Can\Services\RbacRoleSynchronizer;
use Rimba\Can\Support\RbacRoleName;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;

#[Description('Synchronize generated RBAC roles for OrgUnits and OrgTeams.')]
#[Signature('rimba:rbac-sync')]
final class SyncRbacRolesCommand extends Command
{
    public function handle(RbacRoleSynchronizer $roles): int
    {
        $unitResponsibilities = array_values(config(
            'bites.can.rbac.org_unit_roles',
            ['owner', 'member'],
        ));

        $teamResponsibilities = array_values(config(
            'bites.can.rbac.org_team_roles',
            [
                'captain',
                'scout',
                'player',
                'quartermaster',
                'tactician',
                'coach',
            ],
        ));

        $unitCount = 0;
        $teamCount = 0;

        OrgUnit::query()
            ->orderBy('id')
            ->chunkById(100, function ($units) use (
                $roles,
                $unitResponsibilities,
                &$unitCount,
            ): void {
                foreach ($units as $unit) {
                    $roles->syncScope(
                        namespace: RbacRoleName::UNIT,
                        scope: $unit->nick(),
                        responsibilities: $unitResponsibilities,
                    );

                    $unitCount++;
                }
            });

        OrgTeam::query()
            ->orderBy('id')
            ->chunkById(100, function ($teams) use (
                $roles,
                $teamResponsibilities,
                &$teamCount,
            ): void {
                foreach ($teams as $team) {
                    $roles->syncScope(
                        namespace: RbacRoleName::TEAM,
                        scope: $team->nick(),
                        responsibilities: $teamResponsibilities,
                    );

                    $teamCount++;
                }
            });

        $this->table(
            ['Scope Type', 'Records synchronized'],
            [
                ['OrgUnit', $unitCount],
                ['OrgTeam', $teamCount],
            ],
        );

        $this->components->info('RBAC role synchronization completed.');

        return self::SUCCESS;
    }
}
