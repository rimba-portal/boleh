<?php

declare(strict_types=1);

namespace Rimba\Can\Observers;

use Rimba\Can\Services\RbacRoleSynchronizer;
use Rimba\Can\Support\RbacRoleName;
use Rimba\Organization\Models\OrgUnit;

final class OrgUnitRbacObserver
{
    public bool $afterCommit = true;

    public function __construct(
        private readonly RbacRoleSynchronizer $rbacRoleSynchronizer,
    ) {}

    public function created(OrgUnit $orgUnit): void
    {
        $this->sync($orgUnit);
    }

    public function updated(OrgUnit $orgUnit): void
    {
        if ($orgUnit->wasChanged(['code', 'name'])) {
            $this->rbacRoleSynchronizer->renameScope(
                namespace: RbacRoleName::UNIT,
                oldScope: $this->originalNick($orgUnit),
                newScope: $orgUnit->nick(),
                responsibilities: $this->responsibilities(),
            );
        }

        $this->sync($orgUnit);
    }

    public function deleted(OrgUnit $orgUnit): void
    {
        $this->rbacRoleSynchronizer->deleteScope(
            namespace: RbacRoleName::UNIT,
            scope: $orgUnit->nick(),
            responsibilities: $this->responsibilities(),
        );
    }

    private function sync(OrgUnit $orgUnit): void
    {
        $this->rbacRoleSynchronizer->syncScope(
            namespace: RbacRoleName::UNIT,
            scope: $orgUnit->nick(),
            responsibilities: $this->responsibilities(),
        );
    }

    /** @return array<int, string> */
    private function responsibilities(): array
    {
        return array_values(config(
            'bites.can.rbac.org_unit_roles',
            ['owner', 'member'],
        ));
    }

    private function originalNick(OrgUnit $orgUnit): string
    {
        $code = trim((string) $orgUnit->getOriginal('code'));
        $name = trim((string) $orgUnit->getOriginal('name'));

        return $code !== '' ? $code : $name;
    }
}
