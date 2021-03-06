<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\OpenApi\ShouldNeverBeCalledParamDescription;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\SubsequentRule\Enum;
use Params\FirstRule\GetInt;
use Params\FirstRule\GetIntOrDefault;
use Params\FirstRule\GetOptionalInt;
use Params\FirstRule\GetOptionalString;
use Params\FirstRule\GetString;
use Params\FirstRule\GetStringOrDefault;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MaxLength;
use Params\SubsequentRule\MinIntValue;
use Params\SubsequentRule\MinLength;
use Params\SubsequentRule\MultipleEnum;
use Params\SubsequentRule\NotNull;
use Params\SubsequentRule\PositiveInt;
use Params\SubsequentRule\SkipIfNull;
use Params\SubsequentRule\Trim;
use Params\SubsequentRule\ValidDate;
use Params\SubsequentRule\ValidDatetime;
use ParamsTest\BaseTestCase;
use Params\SubsequentRule\AlwaysEndsRule;
use Params\Exception\OpenApiException;
use Params\SubsequentRule\NullIfEmpty;
use VarMap\ArrayVarMap;

/**
 * @coversNothing
 */
class DescriptionTest extends BaseTestCase
{
    public function testEnum()
    {
        $values = [
            'available',
            'pending',
            'sold'
        ];
        $schemaExpectations = [
            'enum' => $values,
        ];
        $varMap = new ArrayVarMap([]);
        $rules =  [
            'value' => [
                new GetString(),
                new Enum($values),
            ],
        ];
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testRequired()
    {
        $descriptionExpectations = [
            'required' => true,
        ];
        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performFullTest([], $descriptionExpectations, $rules);
    }

    public function testMinLength()
    {
        $schemaExpectations = [
            'minLength' => RequiredStringExample::MIN_LENGTH,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaxLength()
    {
        $schemaExpectations = [
            'maxLength' => RequiredStringExample::MAX_LENGTH,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testInt()
    {
        $descriptionExpectations = [
            'required' => true
        ];

        $schemaExpectations = [
            'type' => 'integer'
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetInt()
            ],
        ];

        $this->performFullTest($schemaExpectations, $descriptionExpectations, $rules);
    }

    public function testIntOrDefault()
    {
        $default = 5;
        $schemaExpectations = [
            'type' => 'integer',
            'default' => $default
        ];
        $paramExpectations = [
            'required' => false,
        ];
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetIntOrDefault($default)
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testStringOrDefault()
    {
        $default = 'foo';
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string',
            'default' => $default
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetStringOrDefault($default)
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalInt()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'integer'
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetOptionalInt()
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalString()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string'
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetOptionalString()
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testMinInt()
    {
        $maxValue = 10;
        $schemaExpectations = [
            'minimum' => $maxValue,
            'exclusiveMinimum' => false
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetInt(),
                new MinIntValue($maxValue)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaximumLength()
    {
        $maxLength = 10;
        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString(),
                new MaxLength($maxLength)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesValidMinimumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @dataProvider providesValidMinimumLength
     */
    public function testMininumLength($minLength)
    {
        $schemaExpectations = [
            'minLength' => $minLength,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString(),
                new MinLength($minLength)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesInvalidMininumLength()
    {
        return [[0], [-1], [-2], [-3] ];
    }

    /**
     * @param $minLength
     * @dataProvider providesInvalidMininumLength
     */
    public function testInvalidMininumLength($minLength)
    {
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString(),
                new MinLength($minLength)
            ],
        ];

        $this->expectException(OpenApiException::class);
        OpenApiV300ParamDescription::createFromRules($rules);
    }


    public function providesInvalidMaximumLength()
    {
        return [[0], [-1] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesInvalidMaximumLength
     */
    public function testInvalidMaximumLength($maxLength)
    {
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString(),
                new MaxLength($maxLength)
            ],
        ];

        $this->expectException(OpenApiException::class);
        OpenApiV300ParamDescription::createFromRules($rules);
    }

    public function providesValidMaximumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesValidMaximumLength
     */
    public function testValidMaximumLength($maxLength)
    {
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString(),
                new MaxLength($maxLength)
            ],
        ];

        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testEmptySchema()
    {
        $description = new OpenApiV300ParamDescription();
        $description->setName('testing');
        $result = $description->toArray();
        $this->assertEquals(['name' => 'testing'], $result);
    }

    public function testMaxInt()
    {
        $maxValue = 45;
        $schemaExpectations = [
            'maximum' => $maxValue,
            'exclusiveMaximum' => false
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetInt(),
                new MaxIntValue($maxValue)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testPositiveInt()
    {
        $schemaExpectations = [
            'minimum' => 0,
            'exclusiveMinimum' => false,
            'type' => 'integer'
        ];

        $rules = [
            'value' => [
                new PositiveInt(),
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testSkipIfNull()
    {
        $schemaExpectations = [
            'nullable' => true
        ];
        $rules = [
            'value' => [
                new SkipIfNull()
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testValidDate()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date'
        ];
        $rules = [
            'value' => [
                new ValidDate()
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testValidDateTime()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date-time'
        ];
        $rules = [
            'value' => [
                new ValidDatetime()
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }


    private function performSchemaTest($schemaExpectations, $rules)
    {
        $paramDescription = OpenApiV300ParamDescription::createFromRules($rules);

        $this->assertCount(1, $paramDescription);
        $statusDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $statusDescription);
        $schema = $statusDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $schema, "Schema missing key [$key]. Schema is " .json_encode($schema));
            $this->assertEquals($value, $schema[$key]);
        }
    }


    private function performFullTest($schemaExpectations, $paramExpectations, $rules)
    {
        $paramDescription = OpenApiV300ParamDescription::createFromRules($rules);

        $this->assertCount(1, $paramDescription);
        $openApiDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $openApiDescription);
        $schema = $openApiDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $schema, "Schema missing key [$key]. Schema is " .json_encode($schema));
            $this->assertEquals($value, $schema[$key]);
        }

        foreach ($paramExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $openApiDescription, "openApiDescription missing key [$key]. Description is " .json_encode($openApiDescription));
            $this->assertEquals($value, $openApiDescription[$key]);
        }
    }

    public function testNonStringEnumThrows()
    {
        $description = new OpenApiV300ParamDescription();
        $this->expectException(OpenApiException::class);
        $description->setEnum(['foo', 5]);
    }

    /**
     *
     */
    public function testCoverageOnly()
    {
        $description = new ShouldNeverBeCalledParamDescription();
        $trimRule = new Trim();
        $trimRule->updateParamDescription($description);

        $notNullRule = new NotNull();
        $notNullRule->updateParamDescription($description);

        $alwaysEndsRule = new AlwaysEndsRule(5);
        $alwaysEndsRule->updateParamDescription($description);
    }

    /**
     * @covers \Params\SubsequentRule\NullIfEmpty
     */
    public function testNullIfEmpty()
    {
        $rule = new NullIfEmpty();

        $description = new OpenApiV300ParamDescription();
        $rule->updateParamDescription($description);
        $this->assertTrue($description->getNullAllowed());
    }
}
