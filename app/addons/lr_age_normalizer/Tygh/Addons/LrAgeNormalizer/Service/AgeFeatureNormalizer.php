<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Addons\LrAgeNormalizer\Service;

class AgeFeatureNormalizer
{
    /** @var int Minimal supported age */
    protected int $minimal_age = 1;

    /** @var int Maximal supported age */
    protected int $maximal_age = 16;

    /**
     * Normalizes raw age values into exact target ages.
     *
     * @param array<int, string> $raw_values Raw values
     *
     * @return array<string, array<int, int|string>>
     */
    public function normalize(array $raw_values): array
    {
        $ages = [];
        $unparsed_values = [];

        foreach ($raw_values as $raw_value) {
            foreach ($this->splitCombinedValue((string) $raw_value) as $value_part) {
                $canonical_age = $this->extractCanonicalAge($value_part);
                if ($canonical_age !== null) {
                    $ages[] = $canonical_age;
                    continue;
                }

                $parsed_ages = $this->parseValueToAges($value_part);
                if (empty($parsed_ages)) {
                    $unparsed_values[] = trim($value_part);
                    continue;
                }

                foreach ($parsed_ages as $parsed_age) {
                    $ages[] = $parsed_age;
                }
            }
        }

        sort($ages);
        $ages = array_values(array_unique($ages));
        $unparsed_values = array_values(array_unique(array_filter($unparsed_values)));

        return [
            'ages' => $ages,
            'unparsed_values' => $unparsed_values,
        ];
    }

    /**
     * Extracts age from an already canonical target variant label.
     *
     * @param string $value Variant value
     *
     * @return int|null
     */
    public function extractCanonicalAge(string $value): ?int
    {
        $normalized_value = $this->normalizeValue($value);

        if (!preg_match('/^(\d+)(?:\s*(?:лет|года|год|г))?$/u', $normalized_value, $matches)) {
            return null;
        }

        $age = (int) $matches[1];

        if ($age < $this->minimal_age || $age > $this->maximal_age) {
            return null;
        }

        return $age;
    }

    /**
     * Gets all supported exact target ages.
     *
     * @return array<int, int>
     */
    public function getCanonicalAges(): array
    {
        return range($this->minimal_age, $this->maximal_age);
    }

    /**
     * Splits combined raw values by separators, but keeps decimal commas intact.
     *
     * @param string $value Raw value
     *
     * @return array<int, string>
     */
    protected function splitCombinedValue(string $value): array
    {
        $parts = preg_split('/(?<!\d),(?!\d)|[;\r\n]+/u', (string) $value);
        if (!is_array($parts)) {
            return [];
        }

        $result = [];

        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part === '') {
                continue;
            }

            $result[] = $part;
        }

        return $result;
    }

    /**
     * Parses a single raw source value into exact ages.
     *
     * @param string $value Raw value
     *
     * @return array<int, int>
     */
    protected function parseValueToAges(string $value): array
    {
        $normalized_value = $this->normalizeValue($value);
        if ($normalized_value === '') {
            return [];
        }

        if (
            preg_match('/^до\s*(\d+(?:[.,]\d+)?)\s*(?:х|x)?(?:\s*(?:лет|года|год|г))?$/u', $normalized_value, $matches)
        ) {
            return $this->mapRangeToAges((float) $this->minimal_age, $this->toFloat($matches[1]), false);
        }

        if (preg_match('/^(?:с\s*)?(\d+(?:[.,]\d+)?)\s*\+\s*(?:лет|года|год|г)?$/u', $normalized_value, $matches)) {
            return $this->mapRangeToAges($this->toFloat($matches[1]), null, true);
        }

        if (preg_match('/^с\s*(\d+(?:[.,]\d+)?)(?:\s*(?:лет|года|год|г))?$/u', $normalized_value, $matches)) {
            return $this->mapRangeToAges($this->toFloat($matches[1]), null, true);
        }

        if (
            preg_match(
                '/^от\s*(\d+(?:[.,]\d+)?)\s*до\s*(\d+(?:[.,]\d+)?)(?:\s*(?:лет|года|год|г))?$/u',
                $normalized_value,
                $matches
            )
        ) {
            return $this->mapRangeToAges($this->toFloat($matches[1]), $this->toFloat($matches[2]), false);
        }

        if (
            preg_match(
                '/^(\d+(?:[.,]\d+)?)\s*-\s*(\d+(?:[.,]\d+)?)(?:\s*(?:лет|года|год|г))?$/u',
                $normalized_value,
                $matches
            )
        ) {
            return $this->mapRangeToAges($this->toFloat($matches[1]), $this->toFloat($matches[2]), false);
        }

        if (preg_match('/^(\d+(?:[.,]\d+)?)(?:\s*(?:лет|года|год|г))?$/u', $normalized_value, $matches)) {
            $age = $this->toFloat($matches[1]);

            return $this->mapRangeToAges($age, $age, false);
        }

        return [];
    }

    /**
     * Maps range boundaries to supported integer ages.
     *
     * @param float      $min_age       Minimal age
     * @param float|null $max_age       Maximal age
     * @param bool       $is_open_ended Whether range is open ended
     *
     * @return array<int, int>
     */
    protected function mapRangeToAges(float $min_age, ?float $max_age = null, bool $is_open_ended = false): array
    {
        if ($max_age !== null && $min_age > $max_age) {
            $buffer = $min_age;
            $min_age = $max_age;
            $max_age = $buffer;
        }

        $ages = [];

        foreach ($this->getCanonicalAges() as $age) {
            if ($is_open_ended) {
                if ((float) $age >= $min_age) {
                    $ages[] = $age;
                }

                continue;
            }

            if ($max_age !== null && (float) $age >= $min_age && (float) $age <= $max_age) {
                $ages[] = $age;
            }
        }

        return $ages;
    }

    /**
     * Normalizes raw string before regex parsing.
     *
     * @param string $value Raw value
     *
     * @return string
     */
    protected function normalizeValue(string $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = str_replace(['ё', '–', '—', '−'], ['е', '-', '-', '-'], $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return is_string($value) ? trim($value) : '';
    }

    /**
     * Converts localized numeric string into float.
     *
     * @param string $value Numeric value
     *
     * @return float
     */
    protected function toFloat(string $value): float
    {
        return (float) str_replace(',', '.', (string) $value);
    }
}
