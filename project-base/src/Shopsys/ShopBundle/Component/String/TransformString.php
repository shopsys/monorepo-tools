<?php

namespace Shopsys\ShopBundle\Component\String;

class TransformString
{
    /**
     * @param string $string
     * @return string
     */
    public static function safeFilename($string)
    {
        $string = preg_replace('~[^-\\.\\pL0-9_]+~u', '_', $string);
        $string = preg_replace('~[\\.]{2,}~u', '.', $string);
        $string = trim($string, '_');
        $string = self::toAscii($string);
        $string = preg_replace('~[^-\\.a-zA-Z0-9_]+~', '', $string);
        $string = ltrim($string, '.');

        return $string;
    }

    /**
     * @param string $value
     * @return string|null
     */
    public static function emptyToNull($value)
    {
        return $value === '' ? null : $value;
    }

    /**
     * @param string $string
     * @return string
     * @link http://php.vrana.cz/vytvoreni-pratelskeho-url.php
     */
    public static function stringToFriendlyUrlSlug($string)
    {
        $slug = $string;
        $slug = preg_replace('~[^\\pL0-9_]+~u', '-', $slug);
        $slug = trim($slug, '-');
        $slug = self::toAscii($slug);
        $slug = strtolower($slug);
        $slug = preg_replace('~[^-a-z0-9_]+~', '', $slug);

        return $slug;
    }

    /**
     * Transforms arbitrary string (natural sentence, under_score, PascalCase, ...) into one ascii camelCase string
     *
     * @see \Tests\ShopBundle\Unit\Component\String\TransformStringTest::stringToCamelCaseProvider() for example usages
     *
     * @param string $string
     * @return string
     */
    public static function stringToCamelCase($string)
    {
        // convert everything apart from letters and numbers into spaces
        $string = preg_replace('~[^\\pL0-9]+~u', ' ', $string);
        // transliterate into ascii
        $string = self::toAscii($string);
        // remove special characters after transliteration
        $string = preg_replace('~[^a-zA-Z0-9 ]~', '', $string);
        // preserve camel case by splitting words with spaces
        $string = preg_replace('~([a-z])([A-Z])~', '$1 $2', $string);
        // capitalize only first letter of every word
        $string = ucwords(strtolower($string), ' ');
        // squash words
        $string = str_replace(' ', '', $string);
        // lowercase first letter
        $string = lcfirst($string);

        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    private static function toAscii($string)
    {
        return iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $string);
    }
}
