<?php

/**
 * Zelasli Framework
 *
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli
 */

namespace Zelasli;

class Validation {
    /**
     * Checks if a value is a string of alphabetic
     *
     * @param string $value
     *
     * @return bool
     */
    public static function alpha(string $value)
    {
        return self::regex($value, '/^([[:alpha:]]+)$/');
    }

    /**
     * Checks if a value is a string of alphanumeric
     *
     * @param string $value
     *
     * @return bool
     */
    public static function alnum(string $value)
    {
        return self::regex($value, '/^([[:alnum:]]+)$/');
    }

    /**
     * Checks if a value is a valid boolean-like
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function boolean($value)
    {
        return in_array($value, [false, true, 0, 1, '0', '1'], true);
    }

    /**
     * Checks if a value is before the date. All most  be a valid date string
     *
     * @param string $value
     * @param string $date
     *
     * @return bool
     */
    public static function dateAfter(string $value, string $date) {
        $date_before = strtotime($date);
        $date = strtotime($value);

        return $date >= $date_before;
    }

    /**
     * Checks if a value is before the date. All most  be a valid date string
     *
     * @param string $value
     * @param string $date
     *
     * @return bool
     */
    public static function dateBefore(string $value, string $date) {
        $date_before = strtotime($date);
        $date = strtotime($value);

        return $date <= $date_before;
    }

    /**
     * Validates if value is a valid email address
     *
     * @param string $value
     *
     * @return bool
     */
    public static function email(string $value)
    {
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validates a value is matches second value
     *
     * @param mixed $value
     * @param mixed $value2
     * @param bool $identical
     *
     * @return bool
     */
    public static function equalTo($value, $value2, $identical = false)
    {
        if ($identical) {
            return $value === $value2;
        } else {
            return $value == $value2;
        }
    }

    /**
     * Checks if a value is a falsey
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function falsey($value)
    {
        return in_array($value, [false, 0, '0'], true);
    }

    /**
     * Checks if a value is the given list
     *
     * @param mixed $value
     * @param array $possibleValues
     *
     * @return bool
     */
    public static function in($value, array $possibleValues)
    {
        return in_array($value, $possibleValues, true);
    }

    /**
     * Checks if a length of a value is not greater than max
     *
     * @param mixed $value
     * @param int $max
     *
     * @return bool
     */
    public static function maxLength($value, int $max)
    {
        return strlen((string) $value) <= $max;
    }

    /**
     * Checks if a bytes length of a value is not greater than max
     *
     * @param mixed $value
     * @param int $max
     *
     * @return bool
     */
    public static function maxLengthBytes($value, $max)
    {
        return mb_strlen((string) $value) <= $max;
    }

    /**
     * Checks if a length of a value is not less than min
     *
     * @param mixed $value
     * @param int $max
     *
     * @return bool
     */
    public static function minLength($value, $min)
    {
        return strlen((string) $value) >= $min;
    }

    /**
     * Checks if a bytes length of a value is not less than max
     *
     * @param mixed $value
     * @param int $max
     *
     * @return bool
     */
    public static function minLengthBytes($value, $min)
    {
        return mb_strlen((string) $value) >= $min;
    }

    /**
     * Validates a value does not matches second value
     * @param mixed $value
     * @param mixed $value2
     * @param bool $identical
     *
     * @return bool
     */
    public static function notEqualTo($value, $value2, bool $identical = false)
    {
        if ($identical) {
            return $value !== $value2;
        } else {
            return $value != $value2;
        }
    }

    /**
     * Checks a value is not in the given list values
     *
     * @param mixed $value
     * @param array $possibleValues
     *
     * @return bool
     */
    public static function notIn($value, array $possibleValues)
    {
        return !in_array($value, $possibleValues, true);
    }

    /**
     * Validate a value is a numeric or string of numeric
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function numeric($value)
    {
        return is_numeric($value);
    }

    /**
     * Checks a value is within the given range
     *
     * @param mixed $value
     * @param float $from
     * @param float $to
     */
    public static function range($value, $from = 1, $to = INF)
    {
        return self::numeric($value) && (
            (float) $value >= $from && (float) $value <= $to
        );
    }

    /**
     * Validates a value is a string
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function string(string $value, $min = 1, $max = PHP_INT_MAX)
    {
        if (is_string($value)) {
            return self::minLength($value, $min) &&
                self::maxLength($value, $max);
        }

        return false;
    }

    /** */
    public static function regex(string $value, $pattern)
    {
        return is_scalar($value) && (bool) preg_match($pattern, $value);
    }

    /**
     * Check if a given value is not empty
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function required($value)
    {
        return !empty($value);
    }

    /**
     * Validates a value is required if value2 is equal to check
     *
     * @param mixed $value
     * @param mixed $value2
     * @param mixed $check
     *
     * @return bool
     */
    public static function requiredIf($value, $value2, $check) {
        return !empty($value) && ($value2 == $check);
    }

    /**
     * Validates a value is required if check is in the given list
     *
     * @param mixed $value
     * @param array $list
     * @param mixed $check
     *
     * @return bool
     */
    public static function requiredIfIn($value, array $list, $check) {
        return !empty($value) && in_array($check, $list);
    }

    /**
     * Validates a value is required if check is not in the given list
     *
     * @param mixed $value
     * @param array $list
     * @param mixed $check
     *
     * @return bool
     */
    public static function requiredIfNotIn($value, array $list, $check) {
        return !empty($value) && in_array($check, $list);
    }

    /**
     * Checks if a value is a truethy
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function truethy($value)
    {
        return in_array($value, [true, 1, '1'], true);
    }
}
