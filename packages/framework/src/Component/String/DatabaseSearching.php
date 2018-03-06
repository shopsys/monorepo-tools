<?php

namespace Shopsys\FrameworkBundle\Component\String;

class DatabaseSearching
{
    /**
     * @param string $string
     * @return string
     */
    public static function getLikeSearchString($string)
    {
        return str_replace(
            ['%', '_', '*', '?'],
            ['\%', '\_', '%', '_'],
            $string
        );
    }

    /**
     * @param string|null $string
     * @return string
     */
    public static function getFullTextLikeSearchString($string)
    {
        return '%' . self::getLikeSearchString($string) . '%';
    }
}
