<?php

declare(strict_types=1);

namespace Params\Create;

use Params\Params;
use Psr\Http\Message\ServerRequestInterface;
use VarMap\Psr7VarMap;

/**
 * Use this trait when the parameters arrive as named parameters e.g
 * either as query string parameters, form elements, or other form body.
 */
trait CreateFromRequest
{
    /**
     * @param ServerRequestInterface $request
     * @return self
     * @throws \Params\Exception\RulesEmptyException
     * @throws \Params\Exception\ValidationException
     */
    public static function createFromRequest(ServerRequestInterface $request)
    {
        $variableMap = new Psr7VarMap($request);

        $rules = static::getRules();

        $object = Params::create(static::class, $rules, $variableMap);
        /** @var $object self */
        return $object;
    }
}
