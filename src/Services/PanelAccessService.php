<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Rimba\Can\Contracts\AuthorizationServiceContract;
use Rimba\Can\Contracts\PanelAccessResolverContract;

final readonly class PanelAccessService implements PanelAccessResolverContract
{
    public function __construct(private AuthorizationServiceContract $authorizationServiceContract) {}

    public function canAccess(Authenticatable $subject, string $panelId): bool
    {
        $permission = sprintf(config('bites_auth.panels.permission_pattern'), Str::snake($panelId));

        return $this->authorizationServiceContract->allows($subject, $permission);
    }

    public function accessiblePanels(Authenticatable $subject): array
    {
        return collect(config('bites_auth.panels.priority', []))
            ->filter(fn (string $panel): bool => $this->canAccess($subject, $panel))
            ->values()->all();
    }

    public function destinationFor(Authenticatable $subject): string
    {
        return $this->accessiblePanels($subject)[0] ?? (string) config('bites_auth.panels.fallback', 'lobby');
    }
}
