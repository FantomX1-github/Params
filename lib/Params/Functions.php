<?php

declare(strict_types=1);

namespace Params;

use Params\Value\Ordering;

class Functions
{
    /**
     * Separates an order parameter such as "+name", into the 'name' and
     * 'ordering' parts.
     * @param $part
     * @return array
     */
    public static function normalise_order_parameter($part)
    {
        if (substr($part, 0, 1) === "+") {
            return [substr($part, 1), Ordering::ASC];
        }

        if (substr($part, 0, 1) === "-") {
            return [substr($part, 1), Ordering::DESC];
        }

        return [$part, Ordering::ASC];
    }

    /**
     * @param $name string The name of the variable
     * @param $value mixed The value of the variable
     * @return null|string returns an error string, when there is an error
     */
    public static function check_only_digits(string $name, $value)
    {
        if (is_int($value) === true) {
            return null;
        }

        $count = preg_match("/[^0-9]+/", $value, $matches, PREG_OFFSET_CAPTURE);

        if ($count) {
            $badCharPosition = $matches[0][1];
            $message = sprintf(
                "Value for [$name] must contain only digits. Non-digit found at position %d.",
                $badCharPosition
            );
            return $message;
        }

        return null;
    }
}
