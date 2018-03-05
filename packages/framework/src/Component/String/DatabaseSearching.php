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
        // LIKE pattern must not end with escape character in Postgres
        $string = rtrim($string, '\\');
        $string = str_replace(
            ['%', '_', '*', '?'],
            ['\%', '\_', '%', '_'],
            $string
        );
        return $string;
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
