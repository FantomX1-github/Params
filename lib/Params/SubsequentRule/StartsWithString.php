<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * Convert the value to null if the string is empty, and provides
 * a final result
 */
class StartsWithString implements SubsequentRule
{
    /** @var string  */
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (strpos((string)$value, $this->prefix) !== 0) {
            $message = sprintf(
                "The string for [%s] must start with [%s].",
                $name,
                $this->prefix
            );

            return ValidationResult::errorResult($name, $message);
        }

        // This rule does not modify the value
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // If we are allowing null, then parameter must be nullable
        // right?
        $paramDescription->setNullAllowed();
    }
}
