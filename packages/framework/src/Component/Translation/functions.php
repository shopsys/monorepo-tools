<?php

use Shopsys\FrameworkBundle\Component\Translation\Translator;

/**
 * @param string $id
 * @param array $parameters
 * @param string|null $domain
 * @param string|null $locale
 * @return string
 */
function t($id, array $parameters = [], $domain = null, $locale = null)
{
    return Translator::staticTrans($id, $parameters, $domain, $locale);
}

/**
 * @param string $id
 * @param int $number
 * @param array $parameters
 * @param string|null $domain
 * @param string|null $locale
 * @return string
 */
function tc($id, $number, array $parameters = [], $domain = null, $locale = null)
{
    return Translator::staticTransChoice($id, $number, $parameters, $domain, $locale);
}
