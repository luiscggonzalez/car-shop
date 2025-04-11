<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\PhpUnit\Constraint;

use Illuminate\Support\Str;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory;
use Tests\Shared\Infrastructure\PhpUnit\Comparator\AggregateRootArraySimilarComparator;
use Tests\Shared\Infrastructure\PhpUnit\Comparator\AggregateRootSimilarComparator;
use Tests\Shared\Infrastructure\PhpUnit\Comparator\DateTimeSimilarComparator;
use Tests\Shared\Infrastructure\PhpUnit\Comparator\DateTimeStringSimilarComparator;
use Tests\Shared\Infrastructure\PhpUnit\Comparator\DomainEventArraySimilarComparator;
use Tests\Shared\Infrastructure\PhpUnit\Comparator\DomainEventSimilarComparator;

final class ConstraintIsSimilar extends Constraint
{
    /**
     * @param mixed $value
     * @param float $delta
     */
    public function __construct(private $value, private $delta = 0.0)
    {
    }

    /**
     * @param mixed $other
     * @param string $description
     * @param bool $returnResult
     */
    public function evaluate($other, $description = '', $returnResult = false): bool
    {
        if ($this->value === $other) {
            return true;
        }

        $isValid = true;
        $comparatorFactory = new Factory();

        $comparatorFactory->register(new AggregateRootArraySimilarComparator());
        $comparatorFactory->register(new AggregateRootSimilarComparator());
        $comparatorFactory->register(new DomainEventArraySimilarComparator());
        $comparatorFactory->register(new DomainEventSimilarComparator());
        $comparatorFactory->register(new DateTimeSimilarComparator());
        $comparatorFactory->register(new DateTimeStringSimilarComparator());

        try {
            $comparator = $comparatorFactory->getComparatorFor($other, $this->value);

            $comparator->assertEquals($this->value, $other, $this->delta);
        } catch (ComparisonFailure $f) {
            if (!$returnResult) {
                throw new ExpectationFailedException(
                    trim($description . "\n" . $f->getMessage()),
                    $f
                );
            }

            $isValid = false;
        }

        return $isValid;
    }

    public function toString(): string
    {
        $delta = '';

        if (is_string($this->value)) {
            if (Str::contains($this->value, "\n")) {
                return 'is equal to <text>';
            }

            return sprintf(
                "is equal to '%s'",
                $this->value
            );
        }

        if (abs($this->delta) > 0.0000001) {
            $delta = sprintf(
                ' with delta <%F>',
                $this->delta
            );
        }

        return sprintf(
            'is equal to %s%s',
            Exporter::export($this->value),
            $delta
        );
    }
}
