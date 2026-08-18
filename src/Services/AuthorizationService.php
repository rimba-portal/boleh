<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rimba\Can\Contracts\AttributeResolverContract;
use Rimba\Can\Contracts\AuthorizationServiceContract;
use Rimba\Can\Enums\RuleOperator;
use Rimba\Can\Models\PermissionRule;

final readonly class AuthorizationService implements AuthorizationServiceContract
{
    public function __construct(private AttributeResolverContract $attributeResolverContract) {}

    public function allows(Authenticatable $subject, string $permission): bool
    {
        if (! method_exists($subject, 'hasPermissionTo') || ! $subject->hasPermissionTo($permission)) {
            return false;
        }

        $permissionModel = config('bites_auth.models.permission');
        $record = $permissionModel::findByName($permission, (string) config('bites_auth.guard', 'web'));
        $rules = PermissionRule::query()->where('permission_id', $record->getKey())->where('is_active', true)->get();
        if ($rules->isEmpty()) {
            return true;
        }

        $attributes = $this->attributeResolverContract->resolve($subject);
        $required = $rules->where('required', true);
        if ($required->contains(fn (PermissionRule $rule): bool => ! $this->matches($attributes, $rule))) {
            return false;
        }

        $alternatives = $rules->where('required', false)->groupBy('group');

        return $alternatives->isEmpty() || $alternatives->contains(
            fn (Collection $group): bool => $group->every(fn (PermissionRule $rule): bool => $this->matches($attributes, $rule))
        );
    }

    public function denies(Authenticatable $subject, string $permission): bool
    {
        return ! $this->allows($subject, $permission);
    }

    private function matches(array $attributes, PermissionRule $rule): bool
    {
        $exists = Arr::has($attributes, $rule->attribute_key);
        $actual = data_get($attributes, $rule->attribute_key);
        $expected = $rule->value;
        $expectedScalar = is_array($expected) && count($expected) === 1 ? array_values($expected)[0] : $expected;

        return match ($rule->operator) {
            RuleOperator::Exists => $exists,
            RuleOperator::NotExists => ! $exists,
            RuleOperator::Equals => $actual == $expectedScalar,
            RuleOperator::NotEquals => $actual != $expectedScalar,
            RuleOperator::In => in_array($actual, Arr::wrap($expected), true),
            RuleOperator::NotIn => ! in_array($actual, Arr::wrap($expected), true),
            RuleOperator::Contains => is_array($actual)
                ? in_array($expectedScalar, $actual, true)
                : str_contains((string) $actual, (string) $expectedScalar),
            RuleOperator::GreaterThan => $actual > $expectedScalar,
            RuleOperator::GreaterThanOrEqual => $actual >= $expectedScalar,
            RuleOperator::LessThan => $actual < $expectedScalar,
            RuleOperator::LessThanOrEqual => $actual <= $expectedScalar,
        };
    }
}
