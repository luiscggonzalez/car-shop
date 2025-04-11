<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\PhpUnit\Comparator;

use DateInterval;
use DateTimeInterface;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\ObjectComparator;

final class DateTimeSimilarComparator extends ObjectComparator
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        return $expected instanceof DateTimeInterface && $actual instanceof DateTimeInterface;
    }

    /**
     * @param DateTimeInterface $expected
     * @param DateTimeInterface $actual
     * @param float $delta
     * @param bool $canonicalize
     * @param bool $ignoreCase
     * @param array<mixed> $processed
     * @throws \DateMalformedIntervalStringException
     */
    public function assertEquals(
        $expected,
        $actual,
        $delta = 0.0,
        $canonicalize = false,
        $ignoreCase = false,
        array &$processed = []
    ): void {
        $normalizedDelta   = $delta === 0.0 ? 10 : $delta;
        $intervalWithDelta = new DateInterval(sprintf('PT%sS', abs($normalizedDelta)));

        // Convert to DateTimeImmutable to use add/sub methods (not available in DateTimeInterface)
        $expectedLowerObj = 
            ($expected instanceof \DateTimeImmutable) ? 
            clone $expected : 
            \DateTimeImmutable::createFromInterface($expected);
            
        $expectedUpperObj = 
            ($expected instanceof \DateTimeImmutable) ? 
            clone $expected : 
            \DateTimeImmutable::createFromInterface($expected);

        $lowerBound = $expectedLowerObj->sub($intervalWithDelta);
        $upperBound = $expectedUpperObj->add($intervalWithDelta);

        if ($actual < $lowerBound || $actual > $upperBound) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                $this->dateTimeToString($expected),
                $this->dateTimeToString($actual),
                'Failed asserting that two DateTime objects are equal.'
            );
        }
    }

    protected function dateTimeToString(DateTimeInterface $datetime): string
    {
        try {
            return $datetime->format(DateTimeInterface::ATOM);

        } catch (\Exception $e) {
            return 'Invalid DateTime object';
        }
    }
}
