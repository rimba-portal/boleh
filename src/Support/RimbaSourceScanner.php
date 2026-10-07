<?php

declare(strict_types=1);

namespace Rimba\Can\Support;

use Illuminate\Filesystem\Filesystem;

final class RimbaSourceScanner
{
    public function __construct(private readonly Filesystem $filesystem) {}

    public function scan(): array
    {
        return [...$this->scanDir(config('bites.can.application.path', base_path('app')), config('bites.can.application.namespace', 'App\\'), 'app', null), ...$this->scanRimba()];
    }

    private function scanRimba(): array
    {
        $root = config('bites.can.rimba.path', base_path('vendor/rimba'));
        if (! is_dir($root)) {
            return [];
        } $out = [];
        foreach ($this->filesystem->directories($root) as $pkg) {
            $src = $pkg.'/src';
            $json = $pkg.'/composer.json';
            if (! is_dir($src) || ! is_file($json)) {
                continue;
            } $data = json_decode(file_get_contents($json) ?: '', true) ?: [];
            foreach (($data['autoload']['psr-4'] ?? []) as $ns => $paths) {
                foreach ((array) $paths as $path) {
                    $dir = realpath($pkg.'/'.$path);
                    if ($dir && is_dir($dir)) {
                        $out = [...$out, ...$this->scanDir($dir, $ns, 'rimba', basename($pkg))];
                    }
                }
            }
        }

return $out;
    }

    private function scanDir(string $dir, string $namespace, string $source, ?string $package): array
    {
        if (! is_dir($dir)) {
            return [];
        } $out = [];
        foreach ($this->filesystem->allFiles($dir) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            } $rel = str_replace([$dir.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], ['', '\\'], $file->getPathname());
            $rel = preg_replace('/\.php$/', '', $rel);
            $out[] = ['class' => trim($namespace, '\\').'\\'.$rel, 'path' => $file->getPathname(), 'source' => $source, 'package' => $package];
        }

return $out;
    }
}
