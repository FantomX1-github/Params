<?php

declare(strict_types = 1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Exception\LogicException;
use Params\ParamsValidator;
use Params\ParamValues;

class MaximumCount implements SubsequentRule
{
    /** @var int  */
    private $maximumCount;

    public const ERROR_TOO_MANY_ELEMENTS = "Number of elements in %s is too large. Max allowed is %d but got %d.";

    public const ERROR_MAXIMUM_COUNT_MINIMUM = "Maximum count must be zero or above.";

    public const ERROR_WRONG_TYPE = "Maximum count can only be applied to an array but tried to operate on %s.";

    /**
     *
     * @param int $maximumCount The maximum number (inclusive) of elements
     */
    public function __construct(int $maximumCount)
    {
        if ($maximumCount < 0) {
            throw new LogicException(self::ERROR_MAXIMUM_COUNT_MINIMUM);
        }

        $this->maximumCount = $maximumCount;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (is_array($value) !== true) {
            $message = sprintf(
                self::ERROR_WRONG_TYPE,
                gettype($value)
            );

            throw new LogicException($message);
        }

        $actualCount = count($value);

        if ($actualCount > $this->maximumCount) {
            $message = sprintf(
                self::ERROR_TOO_MANY_ELEMENTS,
                $name,
                $this->maximumCount,
                $actualCount
            );

            return ValidationResult::errorResult($name, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMinItems($this->maximumCount);
        $paramDescription->setExclusiveMinimum(false);
    }
}
