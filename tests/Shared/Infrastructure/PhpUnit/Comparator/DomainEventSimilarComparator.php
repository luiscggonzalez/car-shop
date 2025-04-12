<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\PhpUnit\Comparator;

use CarShop\Shared\Domain\Bus\Event\DomainEvent;
use PHPUnit\Util\Exporter;
use ReflectionException;
use ReflectionObject;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use Tests\Shared\Domain\TestUtils;

final class DomainEventSimilarComparator extends Comparator
{
    /** @var array<int, string> */
    private static array $ignoredAttributes = ['eventId', 'occurredOn'];

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        $domainEventRootClass = DomainEvent::class;

        return $expected instanceof $domainEventRootClass && $actual instanceof $domainEventRootClass;
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     * @param float $delta
     * @param bool $canonicalize
     * @param bool $ignoreCase
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {

        /** @var \Bus\Event\DomainEvent $expected */
        /** @var \Bus\Event\DomainEvent $actual */
        if (!$this->areSimilar($expected, $actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                Exporter::export($expected),
                Exporter::export($actual),
                'Failed asserting the events are equal.'
            );
        }
    }

    /**
     * @throws ReflectionException
     */
    private function areSimilar(DomainEvent $expected, DomainEvent $actual): bool
    {
        if (!$this->areTheSameClass($expected, $actual)) {
            return false;
        }

        return $this->propertiesAreSimilar($expected, $actual);
    }

    private function areTheSameClass(DomainEvent $expected, DomainEvent $actual): bool
    {
        return $expected::class === $actual::class;
    }

    /**
     * @throws ReflectionException
     */
    private function propertiesAreSimilar(DomainEvent $expected, DomainEvent $actual): bool
    {
        $expectedReflected = new ReflectionObject($expected);
        $actualReflected = new ReflectionObject($actual);

        foreach ($expectedReflected->getProperties() as $expectedReflectedProperty) {
            if (!in_array($expectedReflectedProperty->getName(), self::$ignoredAttributes, false)) {
                $actualReflectedProperty = $actualReflected->getProperty($expectedReflectedProperty->getName());

                $expectedReflectedProperty->setAccessible(true);
                $actualReflectedProperty->setAccessible(true);

                $expectedProperty = $expectedReflectedProperty->getValue($expected);
                $actualProperty = $actualReflectedProperty->getValue($actual);

                if (!TestUtils::isSimilar($expectedProperty, $actualProperty)) {
                    return false;
                }
            }
        }

        return true;
    }
}
