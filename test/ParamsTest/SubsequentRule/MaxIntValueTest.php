<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\MaxIntValue;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class MaxIntValueValidatorTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $maxValue = 256;
        $underValue = $maxValue - 1;
        $exactValue = $maxValue ;
        $overValue = $maxValue + 1;

        return [
            [$maxValue, (string)$underValue, false],
            [$maxValue, (string)$exactValue, false],
            [$maxValue, (string)$overValue, true],

            // TODO - think about these cases.
//            [$maxValue, 125.5, true],
//            [$maxValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\SubsequentRule\MaxIntValue
     */
    public function testValidation(int $maxValue, string $inputValue, bool $expectError)
    {
        $rule = new MaxIntValue($maxValue);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $inputValue, $validator);

        if ($expectError === false) {
            $this->assertEmpty($validationResult->getProblemMessages());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessages());
        }
    }
}
