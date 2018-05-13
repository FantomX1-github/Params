<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Rule\CheckSet;
use Params\Rule\MaxIntValue;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;
use Params\Rule\AlwaysEndsRule;

class ParamsValidatorTest extends BaseTestCase
{
    public function testMissingRuleThrows()
    {
        $validator = new ParamsValidator();
        $this->expectException(\Params\Exception\RulesEmptyException::class);
        $validator->validate('foobar', []);
    }

    public function testInvalidInputThrows()
    {
        $arrayVarMap = new ArrayVarMap([]);

        $rules = [
            new CheckSet($arrayVarMap)



        ];

        $validator = new ParamsValidator();

        $value = $validator->validate('foo', $rules);

        $this->assertNull($value);
        $validationProblems = $validator->getValidationProblems();
        $this->assertEquals(1, count($validationProblems));

        $this->assertStringMatchesFormat(CheckSet::ERROR_MESSAGE, $validationProblems[0]);
    }


    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $arrayVarMap = new ArrayVarMap(['foo' => 5]);
        $rules = [
            new CheckSet($arrayVarMap),
            // This rule will stop processing
            new AlwaysEndsRule($finalValue),
            // this rule would give an error if processing was not stopped.
            new MaxIntValue($finalValue - 5)
        ];

        $validator = new ParamsValidator();

        $value = $validator->validate('foo', $rules);

        $this->assertEquals($finalValue, $value);
        $this->assertEmpty($validator->getValidationProblems());
    }
}
