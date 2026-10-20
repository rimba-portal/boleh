<?php

declare(strict_types=1);

namespace Rimba\Can\Observers;

use Rimba\Can\Services\RbacRoleSynchronizer;
use Rimba\Can\Support\RbacRoleName;
use Rimba\Organization\Models\OrgTeam;

final class OrgTeamRbacObserver
{
    public bool $afterCommit = true;

    public function __construct(
        private readonly RbacRoleSynchronizer $rbacRoleSynchronizer,
    ) {}

    public function created(OrgTeam $orgTeam): void
    {
        $this->sync($orgTeam);
    }

    public function updated(OrgTeam $orgTeam): void
    {
        if ($orgTeam->wasChanged(['code', 'name'])) {
            $this->rbacRoleSynchronizer->renameScope(
                namespace: RbacRoleName::TEAM,
                oldScope: $this->originalNick($orgTeam),
                newScope: $orgTeam->nick(),
                responsibilities: $this->responsibilities(),
            );
        }

        $this->sync($orgTeam);
    }

    public function deleted(OrgTeam $orgTeam): void
    {
        $this->rbacRoleSynchronizer->deleteScope(
            namespace: RbacRoleName::TEAM,
            scope: $orgTeam->nick(),
            responsibilities: $this->responsibilities(),
        );
    }

    private function sync(OrgTeam $orgTeam): void
    {
        $this->rbacRoleSynchronizer->syncScope(
            namespace: RbacRoleName::TEAM,
            scope: $orgTeam->nick(),
            responsibilities: $this->responsibilities(),
        );
    }

    /** @return array<int, string> */
    private function responsibilities(): array
    {
        return array_values(config(
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
    }

    private function originalNick(OrgTeam $orgTeam): string
    {
        $code = trim((string) $orgTeam->getOriginal('code'));
        $name = trim((string) $orgTeam->getOriginal('name'));

        return $code !== '' ? $code : $name;
    }
}
