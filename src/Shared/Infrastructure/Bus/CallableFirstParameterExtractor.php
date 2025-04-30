<?php

namespace CarShop\Shared\Infrastructure\Bus;

use CarShop\Shared\Domain\Bus\Event\DomainEventSubscriber;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\reindex;

final class CallableFirstParameterExtractor
{
    /**
     * @param iterable<object> $callables
     * @return array<string, array<int, HandlerDescriptor>>
     */
    public static function forCallables(iterable $callables): array
    {
        /** @var array<string, array<int, HandlerDescriptor>> $result */
        $result = map(self::unflatten(), reindex(self::classExtractor(new self()), $callables));
        return $result;
    }

    /**
     * @param iterable<DomainEventSubscriber> $callables
     * @return array<string, array<int, HandlerDescriptor>>
     */
    public static function forPipedCallables(iterable $callables): array
    {
        /** @var array<string, array<int, HandlerDescriptor>> $result */
        $result = reduce(self::pipedCallablesReducer(), $callables, []);
        return $result;
    }

    /**
     * @return callable(object): ?string
     */
    private static function classExtractor(self $parameterExtractor): callable
    {
        return static fn(object $handler): ?string => $parameterExtractor->extract($handler);
    }

    /**
     * @return callable(array<string, array<int, HandlerDescriptor>>, DomainEventSubscriber): array<string, array<int, HandlerDescriptor>>
     */
    private static function pipedCallablesReducer(): callable
    {
        return static function (array $subscribers, DomainEventSubscriber $subscriber): array {
            /** @var array<string, array<int, HandlerDescriptor>> $subscribers */
            $subscribedEvents = $subscriber::subscribedTo();

            foreach ($subscribedEvents as $subscribedEvent) {
                /** @var DomainEventSubscriber&callable $subscriber */
                $subscribers[$subscribedEvent][] = new HandlerDescriptor($subscriber);
            }

            /** @var array<string, array<int, HandlerDescriptor>> */
            return $subscribers;
        };
    }

    /**
     * @return callable(callable): array<int, HandlerDescriptor>
     */
    private static function unflatten(): callable
    {
        return static fn(callable $value): array => [
            new HandlerDescriptor($value)
        ];
    }

    public function extract(object $class): ?string
    {
        $reflector = new ReflectionClass($class);
        $method = $reflector->getMethod('__invoke');

        if ($this->hasOnlyOneParameter($method)) {
            return $this->firstParameterClassFrom($method);
        }

        return null;
    }

    private function firstParameterClassFrom(ReflectionMethod $method): string
    {
        /** @var ReflectionNamedType|null $fistParameterType */
        $fistParameterType = $method->getParameters()[0]->getType();

        if ($fistParameterType === null) {
            throw new LogicException('Missing type hint for the first parameter of __invoke');
        }

        return $fistParameterType->getName();
    }

    private function hasOnlyOneParameter(ReflectionMethod $method): bool
    {
        return $method->getNumberOfParameters() === 1;
    }
}
