<?php

declare(strict_types=1);

namespace Rimba\Can\Support;

use Illuminate\Support\Str;

final class RbacRoleName
{
    public const string DELIMITER = '◆';

    public const string UNIT = 'unit_roles';

    public const string TEAM = 'team_roles';

    public const string JOB = 'job_roles';

    public const string GLOBAL = 'global';

    public static function unit(string $scope, string $role): string
    {
        return self::make(self::UNIT, $scope, $role);
    }

    public static function team(string $scope, string $role): string
    {
        return self::make(self::TEAM, $scope, $role);
    }

    public static function job(string $scope, string $role): string
    {
        return self::make(self::JOB, $scope, $role);
    }

    public static function globalJob(string $role): string
    {
        return self::job(self::GLOBAL, $role);
    }

    public static function packageManager(string $package): string
    {
        return self::globalJob(self::normalizeRole($package).'_manager');
    }

    public static function make(string $namespace, string $scope, string $role): string
    {
        if (! in_array($namespace, [self::UNIT, self::TEAM, self::JOB], true)) {
            throw new \InvalidArgumentException("Unsupported RBAC namespace [{$namespace}].");
        }

        return implode(self::DELIMITER, [
            $namespace,
            self::normalizeScope($scope),
            self::normalizeRole($role),
        ]);
    }

    public static function normalizeScope(string $scope): string
    {
        if (strtolower(trim($scope)) === self::GLOBAL) {
            return self::GLOBAL;
        }

        return Str::of($scope)->trim()->upper()
            ->replaceMatches('/[^A-Z0-9_]+/', '_')->trim('_')->toString();
    }

    public static function normalizeRole(string $role): string
    {
        return Str::of($role)->trim()->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')->trim('_')->toString();
    }
}
