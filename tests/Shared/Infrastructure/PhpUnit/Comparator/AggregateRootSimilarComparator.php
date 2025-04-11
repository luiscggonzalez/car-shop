<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\PhpUnit\Comparator;

use App\Shared\Domain\Aggregate\AggregateRoot;
use PHPUnit\Util\Exporter;
use ReflectionException;
use ReflectionObject;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use Tests\Shared\Domain\TestUtils;

final class AggregateRootSimilarComparator extends Comparator
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        $aggregateRootClass = AggregateRoot::class;

        return $expected instanceof $aggregateRootClass && $actual instanceof $aggregateRootClass;
    }

    /**
     * @param AggregateRoot $expected
     * @param AggregateRoot $actual
     * @param float $delta
     * @param bool $canonicalize
     * @param bool $ignoreCase
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {
        $actualEntity = clone $actual;
        $actualEntity->pullDomainEvents();

        if (!$this->aggregateRootsAreSimilar($expected, $actualEntity)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                Exporter::export($expected),
                Exporter::export($actual),
                'Failed asserting the aggregate roots are equal.'
            );
        }
    }

    private function aggregateRootsAreSimilar(AggregateRoot $expected, AggregateRoot $actual): bool
    {
        if (!$this->aggregateRootsAreTheSameClass($expected, $actual)) {
            return false;
        }

        return $this->aggregateRootPropertiesAreSimilar($expected, $actual);
    }

    private function aggregateRootsAreTheSameClass(AggregateRoot $expected, AggregateRoot $actual): bool
    {
        return $expected::class === $actual::class;
    }

    /**
     * @throws ReflectionException
     */
    private function aggregateRootPropertiesAreSimilar(AggregateRoot $expected, AggregateRoot $actual): bool
    {
        $expectedReflected = new ReflectionObject($expected);
        $actualReflected = new ReflectionObject($actual);

        foreach ($expectedReflected->getProperties() as $expectedReflectedProperty) {

            $propertyName = $expectedReflectedProperty->getName();
             if (!$actualReflected->hasProperty($propertyName)) {
                  return false;
             }
            $actualReflectedProperty = $actualReflected->getProperty($propertyName);

            $expectedReflectedProperty->setAccessible(true);
            $actualReflectedProperty->setAccessible(true);

            $expectedProperty = $expectedReflectedProperty->getValue($expected);
            $actualProperty = $actualReflectedProperty->getValue($actual);

            if (!TestUtils::isSimilar($expectedProperty, $actualProperty)) {
                return false;
            }
        }

        return true;
    }
}
