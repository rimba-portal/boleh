<?php

declare(strict_types=1);

namespace Rimba\FilamentAuthorization\Filament\Pages;

use Filament\Pages\Page;

class AccessControlDashboard extends Page
{
    protected static ?string $title = 'Access Control';

    protected string $view = 'rimba-authorization::filament.pages.access-control-dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?int $navigationSort = 1;
}
