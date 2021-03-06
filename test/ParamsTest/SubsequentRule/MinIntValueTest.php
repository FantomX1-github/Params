<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\MinIntValue;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class MinIntValueTest extends BaseTestCase
{
    public function provideMinIntValueCases()
    {
        $minValue = 100;
        $underValue = $minValue - 1;
        $exactValue = $minValue ;
        $overValue = $minValue + 1;

        return [
            [$minValue, (string)$underValue, true],
            [$minValue, (string)$exactValue, false],
            [$minValue, (string)$overValue, false],

            // TODO - think about these cases.
            [$minValue, 'banana', true]
        ];
    }

    /**
     * @dataProvider provideMinIntValueCases
     * @covers \Params\SubsequentRule\MinIntValue
     */
    public function testValidation(int $minValue, string $inputValue, bool $expectError)
    {
        $rule = new MinIntValue($minValue);
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
