<?php

declare(strict_types = 1);

namespace Params\PatchRule;

use Params\PatchOperation\PatchOperation;

class PatchAdd implements PatchRule
{
    /** @var string */
    private $pathRegex;

    /** @var string */
    private $className;

    private $rules;

    /**
     *
     * @param string $pathRegex
     * @param string $className
     */
    public function __construct(string $pathRegex, string $className, array $rules)
    {
        $this->pathRegex = $pathRegex;
        $this->className = $className;
        $this->rules     = $rules;
    }

    /**
     * @return string
     */
    public function getPathRegex(): string
    {
        return $this->pathRegex;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function getOpType(): string
    {
        return PatchOperation::ADD;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
