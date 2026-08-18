<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Description('Preview permissions discovered from registered Filament panels.')]
#[Signature('boleh:scan {--panel=}')]
final class ScanPermissionsCommand extends BolehCommand
{
    public function handle(): int
    {
        $this->line(json_encode($this->definitions(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }
}
