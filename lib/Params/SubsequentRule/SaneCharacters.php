<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\Exception\LogicException;
use Params\OpenApi\ParamDescription;
use Params\ValidationResult;
use Params\ParamsValidator;
use Params\ParamValues;

class SaneCharacters implements SubsequentRule
{
    const ALLOWED_CHAR_TYPES = [
        // Letter
        "\p{L}",

        // Number
        "\p{N}",

        // Punctuation - todo restrict to subtypes
        "\p{P}",

        // Symbol
        "\p{S}",

//        M Mark
//            Mc    Spacing mark
//            Me    Enclosing mark
//            Mn    Non-spacing mark
        "\p{M}",

        // Space separator (not Line separator or Paragraph separator)
        "\p{Zs}",

        // emoticons
        // https://www.fileformat.info/info/unicode/block/emoticons/utf8test.htm
        "\u{1F600}-\u{1F64F}",

        // Dingbats
        "\u{2700}-\u{27B0}",

        // Transport and map symbols
        "\u{1F680}-\u{1F6C0}",

        // Enclosed characters
        "\u{2460}-\u{24FF}",

        // Additional emoticons
        "\u{1F600}-\u{1F636}",

        // Additional transport and map symbols
        "\u{1F680}-\u{1F6FF}",

        // Other additional symbols
        "\u{1F30D}-\u{1F567}",
    ];

    // Emoji blocks are taken from
    // https://apps.timwhitlock.info/emoji/tables/unicode


    // \p{xx} a character with the xx property
    // \P{xx} a character without the xx property
    // \X an extended Unicode sequence

    private $validCharacters;

    /**
     * SaneCharacters constructor.
     */
    public function __construct()
    {
        $pattern = implode(self::ALLOWED_CHAR_TYPES);

        $this->validCharacters = new ValidCharacters($pattern);
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        $validationResult = $this->validCharacters->process($name, $value, $validator);

        // If validation has already failed, return it.
        if (count($validationResult->getProblemMessages()) !== 0) {
            return $validationResult;
        }

        // Any 3 or more combining things.
        $disallowedPattern = "/([\u{0300}-\u{036F}\u{20D0}-\u{20FF}]{3,})/xu";
        $matches = [];
        $count = preg_match($disallowedPattern, $value, $matches, PREG_OFFSET_CAPTURE);

        if ($count === false) {
            throw new LogicException("preg_match failed");
        }

        if ($count !== 0) {
            $badCharPosition = $matches[0][1];
            $message = sprintf(
                "Invalid combining characters found at position %s",
                $badCharPosition
            );
            return ValidationResult::errorResult($name, $message);
        }
        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $this->validCharacters->updateParamDescription($paramDescription);
    }
}
