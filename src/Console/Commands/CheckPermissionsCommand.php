<?php

declare(strict_types=1);

namespace Rimba\Can\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Spatie\Permission\Models\Permission;

#[Description('Check discovered permissions against the database.')]
#[Signature('boleh:check')]
final class CheckPermissionsCommand extends BolehCommand
{
    public function handle(): int
    {
        $d = $this->definitions();
        $guard = config('bites.can.sync.guard', 'web');
        $existing = Permission::query()->where('guard_name', $guard)->pluck('name')->all();
        $names = array_map(fn ($x) => $x->name, $d);
        $missing = array_values(array_diff($names, $existing));
        $orphaned = array_values(array_diff($existing, $names));
        $this->table(['Metric', 'Count'], [['Discovered', count($names)], ['Existing', count($existing)], ['Missing', count($missing)], ['Orphaned', count($orphaned)]]);
        foreach (['Missing' => $missing, 'Orphaned' => $orphaned] as $label => $items) {
            if ($items !== []) {
                $this->warn($label.':');
                foreach ($items as $item) {
                    $this->line('  '.$item);
                }
            }
        }

        return ($missing || $orphaned) ? self::FAILURE : self::SUCCESS;
    }
}
