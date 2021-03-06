<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\MaximumCount;
use Params\Exception\LogicException;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class MaximumCountTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            [3, []], // 3 <= 3
            [3, [1, 2, 3]], // 3 <= 3
            [4, [1, 2, 3]], // 3 <= 4
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\SubsequentRule\MaximumCount
     */
    public function testWorks(int $maximumCount, $values)
    {
        $rule = new MaximumCount($maximumCount);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $values, $validator);
        $this->assertEmpty($validationResult->getProblemMessages());
        $this->assertFalse($validationResult->isFinalResult());
        $this->assertSame($values, $validationResult->getValue());
    }

    public function provideFailsCases()
    {
        return [
            [0, [1, 2, 3]], // 3 > 0
            [3, [1, 2, 3, 4]], // 4 > 3
        ];
    }

    /**
     * @dataProvider provideFailsCases
     * @covers \Params\SubsequentRule\MaximumCount
     */
    public function testFails(int $maximumCount, $values)
    {
        $rule = new MaximumCount($maximumCount);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $values, $validator);
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        'Number of elements in foo is too large. Max allowed is 0 but got 3.'

        $this->assertRegExp(
            stringToRegexp(MaximumCount::ERROR_TOO_MANY_ELEMENTS),
            $validationResult->getProblemMessages()['/foo']
        );
    }

    /**
     * @covers \Params\SubsequentRule\MaximumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(MaximumCount::ERROR_MAXIMUM_COUNT_MINIMUM);
        new MaximumCount(-2);
    }

    /**
     * @covers \Params\SubsequentRule\MaximumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MaximumCount(3);
        $this->expectException(LogicException::class);

        $validator = new ParamsValidator();
        $this->expectExceptionMessageRegExp(
            stringToRegexp(MaximumCount::ERROR_WRONG_TYPE)
        );

        $rule->process('foo', 'a banana', $validator);
    }
}
