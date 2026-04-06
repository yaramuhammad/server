<?php

namespace App\Traits;

trait HasTranslations
{
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->{$field};

        if (!is_array($value)) {
            return $value;
        }

        return $value[$locale] ?? $value['en'] ?? null;
    }

    public function getTranslations(string $field): ?array
    {
        $value = $this->{$field};

        if (is_array($value)) {
            return $value;
        }

        return null;
    }
}
