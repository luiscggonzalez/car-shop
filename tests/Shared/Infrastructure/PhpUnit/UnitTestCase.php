<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\PhpUnit;

use CarShop\Shared\Domain\Bus\Event\DomainEvent;
use CarShop\Shared\Domain\Bus\Event\EventBus;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Tests\Shared\Domain\TestUtils;
use Tests\Shared\Infrastructure\Mockery\MatcherIsSimilar;

abstract class UnitTestCase extends TestCase
{
    /** @var (\CarShop\Shared\Domain\Bus\Event\EventBus&MockInterface)|null */
    private $eventBus = null;

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return T&MockInterface
     */
    protected function mock(string $className): MockInterface
    {
        /** @var MockInterface&T */
        return Mockery::mock($className);
    }

    protected function shouldPublishDomainEvent(DomainEvent $domainEvent): void
    {
        $this->eventBus()
            ->shouldReceive('publish')
            ->once()
            ->with($this->similarTo($domainEvent))
            ->andReturnNull();
    }

    protected function shouldNotPublishDomainEvent(): void
    {
        $this->eventBus()
            ->shouldReceive('publish')
            ->withNoArgs()
            ->andReturnNull();
    }

    /**
     * @return \Bus\Event\EventBus&MockInterface
     */
    protected function eventBus(): EventBus&MockInterface
    {
        return $this->eventBus ??= $this->mock(EventBus::class);
    }

    protected function notify(DomainEvent $event, callable $subscriber): void
    {
        $subscriber($event);
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    protected function isSimilar($expected, $actual): bool
    {
        return TestUtils::isSimilar($expected, $actual);
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    protected function assertSimilar($expected, $actual): void
    {
        TestUtils::assertSimilar($expected, $actual);
    }

    /**
     * @param mixed $value
     * @param float $delta
     */
    protected function similarTo($value, $delta = 0.0): MatcherIsSimilar
    {
        return TestUtils::similarTo($value, $delta);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
