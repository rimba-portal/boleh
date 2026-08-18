<?php

declare(strict_types=1);

namespace Rimba\Can\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Rimba\Can\Contracts\PanelAccessResolverContract;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsurePanelAccess
{
    public function __construct(private PanelAccessResolverContract $panelAccessResolverContract) {}

    public function handle(Request $request, Closure $next, ?string $panelId = null): Response
    {
        $user = Filament::auth()->user();
        if (! $user) {
            return redirect()->guest(Filament::getCurrentPanel()?->getLoginUrl() ?? '/');
        }

        $panelId ??= Filament::getCurrentPanel()?->getId();
        if ($panelId && $this->panelAccessResolverContract->canAccess($user, $panelId)) {
            return $next($request);
        }

        $destination = $this->panelAccessResolverContract->destinationFor($user);
        $panel = Filament::getPanel($destination);
        if ($panel && $destination !== $panelId) {
            return redirect()->to($panel->getUrl());
        }

        abort(403);
    }
}
