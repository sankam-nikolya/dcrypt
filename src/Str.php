<?php

/**
 * Str.php
 * 
 * PHP version 7
 * 
 * @category Dcrypt
 * @package  Dcrypt
 * @author   Michael Meyer (mmeyer2k) <m.meyer2k@gmail.com>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     https://github.com/mmeyer2k/dcrypt
 */

namespace Dcrypt;

/**
 * Provides time-safe string comparison facilities, and safe string operations
 * on systems that have mb_* function overloading enabled.
 * 
 * The functions in this class were inspired by the symfony's StringUtils class. 
 * 
 * @category Dcrypt
 * @package  Dcrypt
 * @author   Michael Meyer (mmeyer2k) <m.meyer2k@gmail.com>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     https://github.com/mmeyer2k/dcrypt
 * @link     https://github.com/symfony/Security/blob/master/Core/Util/StringUtils.php
 * @link     https://php.net/manual/en/mbstring.overload.php
 * @link     https://apigen.ci/github/mmeyer2k/dcrypt/namespace-Dcrypt.html
 */
final class Str
{
    /**
     * Compares two strings in constant time. Strings are hashed before 
     * comparison so information is not leaked when strings are not of
     * equal length.
     *
     * @param string $known       The string of known length to compare against
     * @param string $given       The string that the user can control
     *
     * @return bool
     */
    public static function equal(string $known, string $given): bool
    {
        // We hash the 2 inputs at this point because hash_equals is still 
        // vulnerable to timing attacks when the inputs have different sizes.
        // Inputs are also cast to string like in symfony stringutils.
        $nonce = Random::bytes(32);

        $known = \hash_hmac('sha256', (string) $known, $nonce, true);
        $given = \hash_hmac('sha256', (string) $given, $nonce, true);

        return \hash_equals($known, $given);
    }

    /**
     * Determine the length of the output of a given hash algorithm in bytes.
     * 
     * @param string $algo Name of algorithm to look up
     * 
     * @return int
     */
    public static function hashSize(string $algo): int
    {
        return self::strlen(\hash($algo, 'hash me', true));
    }

    /**
     * Returns the number of bytes in a string.
     *
     * @param string $string The string whose length we wish to obtain
     *
     * @return int
     */
    public static function strlen(string $string): int
    {
        if (\function_exists('mb_strlen')) {
            return \mb_strlen($string, '8bit');
        }

        return \strlen($string); // @codeCoverageIgnore
    }

    /**
     * Returns part of a string.
     *
     * @param string $string The string whose length we wish to obtain
     * @param int    $start
     * @param int    $length
     * 
     * @return string the extracted part of string; or FALSE on failure, or an empty string.
     */
    public static function substr(string $string, int $start, int $length = null): string
    {
        if (\function_exists('mb_substr')) {
            return \mb_substr($string, $start, $length, '8bit');
        }

        return \substr($string, $start, $length); // @codeCoverageIgnore
    }
}
