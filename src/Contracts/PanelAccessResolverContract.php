<?php

declare(strict_types=1);

namespace Rimba\Can\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface PanelAccessResolverContract
{
    public function canAccess(Authenticatable $subject, string $panelId): bool;

    /** @return list<string> */
    public function accessiblePanels(Authenticatable $subject): array;

    public function destinationFor(Authenticatable $subject): string;
}
