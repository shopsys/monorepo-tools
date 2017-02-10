<?php

namespace Shopsys\ShopBundle\Twig;

use Twig_Extension;
use Twig_SimpleFunction;

class VarDumperExtension extends Twig_Extension
{

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new Twig_SimpleFunction(
                'd',
                [$this, 'd']
            ),
        ];
    }

    /**
     * @param mixed $var
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function d($var) {
        d($var);
    }

    public function getName() {
        return 'var_dumper_extension';
    }
}
