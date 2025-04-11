<?php

declare(strict_types=1);

namespace Tests\Shared\Infrastructure\Mockery;

use Mockery\Matcher\MatcherInterface;
use Tests\Shared\Infrastructure\PhpUnit\Constraint\ConstraintIsSimilar;

final readonly class MatcherIsSimilar implements MatcherInterface
{
    private ConstraintIsSimilar $constraint;

    /**
     * @param mixed $value
     * @param float $delta
     */
    public function __construct($value, $delta = 0.0)
    {
        $this->constraint = new ConstraintIsSimilar($value, $delta);
    }

    public function __toString(): string
    {
        return 'Is similar';
    }

    /**
     * @param mixed $actual
     * @return bool
     */
    public function match(&$actual)
    {
        return $this->constraint->evaluate($actual, '', true);
    }
}
