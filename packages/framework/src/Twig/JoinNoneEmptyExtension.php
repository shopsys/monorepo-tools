<?php

namespace Shopsys\FrameworkBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class JoinNoneEmptyExtension extends Twig_Extension
{
    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('joinNoneEmpty', [$this, 'getArray']),
        ];
    }

    /**
     * @return string
     */
    public function getArray(array $array, $glue = ', ')
    {
        return implode($glue, array_filter($array));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'join_none_empty';
    }
}
