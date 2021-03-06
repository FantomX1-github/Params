<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * Checks a value is an float
 */
class FloatInput implements SubsequentRule
{
    /**
     * Convert a generic input value to an integer
     *
     * @param string $name
     * @param mixed $value
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        // TODO - check is null
        if (is_int($value) !== true) {
            $value = (string)$value;
            if (strlen($value) === 0) {
                return ValidationResult::errorResult(
                    $name,
                    "Value is an empty string - must be a floating point number."
                );
            }

            $match = preg_match(
                "~        #delimiter
                    ^           # start of input
                    -?          # minus, optional
                    [0-9]+      # at least one digit
                    (           # begin group
                        \.      # a dot
                        [0-9]+  # at least one digit
                    )           # end of group
                    ?           # group is optional
                    $           # end of input
                ~xD",
                $value
            );

            if ($match !== 1) {
                // TODO - says what position bad character is at.
                return ValidationResult::errorResult($name, "Value must be a floating point number.");
            }
        }

        return ValidationResult::valueResult(floatval($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // todo - this seems like a not needed rule.
    }
}
