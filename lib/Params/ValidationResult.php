<?php

declare(strict_types=1);

namespace Params;

class ValidationResult
{
    /** @var string */
    private $value;

    /** @var string */
    private $problemMessage;

    /** @var bool */
    private $isFinalResult;

    /**
     * ValidationResult constructor.
     * @param $value
     * @param string $problemMessage
     * @param bool $isFinalResult
     */
    private function __construct($value, ?string $problemMessage, bool $isFinalResult)
    {
        $this->value = $value;
        $this->problemMessage = $problemMessage;
        $this->isFinalResult = $isFinalResult;
    }

    /**
     * @param $message
     * @return ValidationResult
     */
    public static function errorResult($message)
    {
        return new self(null, $message, true);
    }

    /**
     * @param $value
     * @return ValidationResult
     */
    public static function valueResult($value)
    {
        return new self($value, null, false);
    }

    /**
     * @param $value
     * @return ValidationResult
     */
    public static function finalValueResult($value)
    {
        return new self($value, null, true);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getProblemMessage(): ?string
    {
        return $this->problemMessage;
    }

    /**
     * @return bool
     */
    public function isFinalResult(): bool
    {
        return $this->isFinalResult;
    }
}
