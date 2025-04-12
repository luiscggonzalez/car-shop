<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\PhpUnit\Comparator;

use CarShop\Shared\Domain\Bus\Event\DomainEvent;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use Tests\Shared\Domain\TestUtils;
use function Lambdish\Phunctional\all;
use function Lambdish\Phunctional\any;
use function Lambdish\Phunctional\instance_of;

final class DomainEventArraySimilarComparator extends Comparator
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        return is_array($expected)
            && is_array($actual)
            && (all(instance_of(DomainEvent::class), $expected)
                && all(instance_of(DomainEvent::class), $actual));
    }

    /**
     * @param array<int, DomainEvent> $expected
     * @param array<int, \Bus\Event\DomainEvent> $actual
     * @param float $delta
     * @param bool $canonicalize
     * @param bool $ignoreCase
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {
        if (!$this->contains($expected, $actual) || count($expected) !== count($actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                Exporter::export($expected),
                Exporter::export($actual),
                'Failed asserting the collection of Events contains all the expected elements.'
            );
        }
    }

    /**
     * @param array<int, DomainEvent> $expectedArray
     * @param array<int, \Bus\Event\DomainEvent> $actualArray
     */
    private function contains(array $expectedArray, array $actualArray): bool
    {
        $exists = static fn(DomainEvent $expected): bool => any(
            static fn(DomainEvent $actual): bool => TestUtils::isSimilar($expected, $actual),
            $actualArray
        );

        return all($exists, $expectedArray);
    }
}
