<?php

declare(strict_types=1);

namespace Rimba\Can\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Can\Contracts\AttributeResolverContract;

final class DefaultAttributeResolver implements AttributeResolverContract
{
    public function resolve(Authenticatable $subject): array
    {
        $column = (string) config('bites_auth.attributes.json_column', 'attributes');
        $raw = $subject->{$column} ?? [];
        if (is_array($raw)) {
            return $raw;
        }

        $relation = (string) config('bites_auth.attributes.relation', 'attributes');
        if (! method_exists($subject, $relation)) {
            return [];
        }

        $key = (string) config('bites_auth.attributes.key_column', 'key');
        $value = (string) config('bites_auth.attributes.value_column', 'value');

        return $subject->{$relation}()->get()->mapWithKeys(
            fn ($attribute): array => [(string) data_get($attribute, $key) => data_get($attribute, $value)]
        )->all();
    }
}
