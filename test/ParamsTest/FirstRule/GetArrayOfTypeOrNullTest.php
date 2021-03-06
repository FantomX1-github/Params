<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\FirstRule\GetArrayOfTypeOrNull;
use ParamsTest\BaseTestCase;
use Params\FirstRule\GetArrayOfType;
use ParamsTest\Integration\ItemParams;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class GetArrayOfTypeOrNullTest extends BaseTestCase
{

    /**
     * @covers \Params\FirstRule\GetArrayOfTypeOrNull
     */
    public function testWorks()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => 'Hello world']
            ],
        ];

        $rule = new GetArrayOfTypeOrNull(ItemParams::class);
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ItemParams::class, $item);
        /** @var ItemParams $item */
        $this->assertSame(5, $item->getFoo());
        $this->assertSame('Hello world', $item->getBar());

        $this->assertCount(0, $result->getProblemMessages());
    }

    /**
     * @covers \Params\FirstRule\GetArrayOfTypeOrNull
     */
    public function testWorksWhenNotSet()
    {
        $data = [];

        $rule = new GetArrayOfTypeOrNull(ItemParams::class);
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
        $this->assertEmpty($result->getProblemMessages());
    }
}
