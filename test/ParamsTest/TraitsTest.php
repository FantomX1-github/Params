<?php

declare(strict_types=1);

namespace ParamsTest;

use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use ParamsTest\Integration\FooParamsCreateFromVarMap;
use ParamsTest\Integration\FooParamsCreateOrErrorFromVarMap;

/**
 * @coversNothing
 * @group patch
 */
class TraitsTest extends BaseTestCase
{
    /**
     * @covers \Params\Create\CreateFromVarMap
     */
    public function testCreateFromVarMap()
    {
        $limitValue = 13;
        $varMap = new ArrayVarMap(['limit' => $limitValue]);
        $fooParams = FooParamsCreateFromVarMap::createFromVarMap($varMap);
        $this->assertInstanceOf(FooParamsCreateFromVarMap::class, $fooParams);
        $this->assertEquals($limitValue, $fooParams->getLimit());
    }

    /**
     * @covers \Params\Create\CreateOrErrorFromVarMap
     */
    public function testCreateOrErrorFromVarMap()
    {
        $limitValue = 13;
        $varMap = new ArrayVarMap(['limit' => $limitValue]);
        [$fooParams, $errors] = FooParamsCreateOrErrorFromVarMap::createOrErrorFromVarMap($varMap);
        $this->assertEmpty($errors);
        $this->assertInstanceOf(FooParamsCreateOrErrorFromVarMap::class, $fooParams);
        /** @var $fooParams FooParamsCreateOrErrorFromVarMap */
        $this->assertEquals($limitValue, $fooParams->getLimit());
    }
}
