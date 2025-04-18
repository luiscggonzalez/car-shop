<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\PhpUnit\Comparator;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\ObjectComparator;
use Throwable;

final class DateTimeStringSimilarComparator extends ObjectComparator
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        return (null !== $actual)
            && is_string($expected)
            && is_string($actual)
            && $this->isValidDateTimeString($expected)
            && $this->isValidDateTimeString($actual);
    }

    /**
     * @param string $expected
     * @param string $actual
     * @param float $delta
     * @param bool $canonicalize
     * @param bool $ignoreCase
     * @param array<mixed> $processed
     */
    public function assertEquals(
        $expected,
        $actual,
        $delta = 0.0,
        $canonicalize = false,
        $ignoreCase = false,
        array &$processed = []
    ): void {
        $expectedDate = new DateTimeImmutable($expected);
        $actualDate = new DateTimeImmutable($actual);

        $normalizedDelta = $delta === 0.0 ? 10 : $delta;
        $intervalWithDelta = new DateInterval(sprintf('PT%sS', abs($normalizedDelta)));

        if ($actualDate < $expectedDate->sub($intervalWithDelta)
            || $actualDate > $expectedDate->add($intervalWithDelta)) {
            throw new ComparisonFailure(
                $expectedDate,
                $actualDate,
                $this->dateTimeToString($expectedDate),
                $this->dateTimeToString($actualDate),
                'Failed asserting that two DateTime strings are equal.'
            );
        }
    }

    protected function dateTimeToString(DateTimeInterface $datetime): string
    {
        try {
            return $datetime->format(DateTimeInterface::ATOM);
        } catch (Throwable $e) {
            return 'Invalid DateTime object';
        }
    }

    private function isValidDateTimeString(string $expected): bool
    {
        $isValid = true;

        try {
            new DateTimeImmutable($expected);
        } catch (Throwable) {
            $isValid = false;
        }
        return $isValid;
    }
}
