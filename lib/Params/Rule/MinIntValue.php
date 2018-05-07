<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;

class MinIntValue implements Rule
{
    /** @var int  */
    private $minValue;

    public function __construct(int $minValue)
    {
        $this->minValue = $minValue;
    }

    public function __invoke(string $name, $value): ValidationResult
    {
        $value = intval($value);
        if ($value < $this->minValue) {
            return ValidationResult::errorResult("Value too small. Min allowed is " . $this->minValue);
        }

        return ValidationResult::valueResult($value);
    }
}
