<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Description('Preview permissions discovered from app and RIMBA packages.')]
#[Signature('boleh:scan')]
final class ScanPermissionsCommand extends BolehCommand
{
    public function handle(): int
    {
        $d = $this->definitions();
        $this->table(['Permission', 'Type', 'Package', 'Source'], array_map(fn ($x): array => [$x->name, $x->type, $x->package ?? 'app', $x->class ?? ''], $d));
        $this->info('Discovered: '.count($d));

        return self::SUCCESS;
    }
}
