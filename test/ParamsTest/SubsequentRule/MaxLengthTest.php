<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\MaxLength;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class MaxLengthTest extends BaseTestCase
{
    public function provideMaxLengthCases()
    {
        $maxLength = 10;
        $underLengthString = str_repeat('a', $maxLength - 1);
        $exactLengthString = str_repeat('a', $maxLength);
        $overLengthString = str_repeat('a', $maxLength + 1);

        return [
            [$maxLength, $underLengthString, false],
            [$maxLength, $exactLengthString, false],
            [$maxLength, $overLengthString, true],
        ];
    }

    /**
     * @dataProvider provideMaxLengthCases
     * @covers \Params\SubsequentRule\MaxLength
     */
    public function testValidation(int $maxLength, string $string, bool $expectError)
    {
        $rule = new MaxLength($maxLength);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $string, $validator);

        if ($expectError === false) {
            $this->assertEmpty($validationResult->getProblemMessages());
        }
        else {
            $this->assertNotNull($validationResult->getProblemMessages());
        }
    }
}
