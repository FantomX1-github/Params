<?php

declare(strict_types = 1);

namespace Params\FirstRule;

use Params\SafeAccess;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Params;
use Params\ParamValues;
use Params\Functions;

class GetArrayOfType implements FirstRule
{
    /** @var string  */
    private $className;

    const ERROR_MESSAGE_NOT_SET = "Value must be set.";

    const ERROR_MESSAGE_NOT_ARRAY = "Value must be an array.";

    const ERROR_MESSAGE_ITEM_NOT_ARRAY = "Values for type '%s' must be an array, but got '%s'. Use GetArrayOfInt|String for single values.";

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ): ValidationResult {
        if ($varMap->has($name) !== true) {
            return ValidationResult::errorResult($name, self::ERROR_MESSAGE_NOT_SET);
        }

        $itemData = $varMap->get($name);

        if (is_array($itemData) !== true) {
            return ValidationResult::errorResult($name, self::ERROR_MESSAGE_NOT_ARRAY);
        }

        $items = [];
        /** @var array<string> $errorsMessages */
        $errorsMessages = [];
        $index = 0;

        foreach ($itemData as $itemDatum) {
            if (is_array($itemDatum) !== true) {
                $message = sprintf(
                    self::ERROR_MESSAGE_ITEM_NOT_ARRAY,
                    $this->className,
                    gettype($itemDatum)
                );

                return ValidationResult::errorResult($name, $message);
            }

            $dataVarMap = new ArrayVarMap($itemDatum);
            $rules = call_user_func([$this->className, 'getRules'], $dataVarMap);

            [$item, $errors] = Params::createOrError($this->className, $rules, $dataVarMap);

            if ($errors !== null) {
                /**
                 * @var string $key
                 * @var string $error
                 */
                foreach ($errors as $key => $error) {
                    $errorsMessages['/' . $name . '/' . $index . $key] = $error;
                }
            }

            $index += 1;
            $items[] = $item;
        }


        if (count($errorsMessages) !== 0) {
            return ValidationResult::errorsResult($errorsMessages);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // TODO - implement
    }
}
