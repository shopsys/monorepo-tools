<?php

use Symfony\Component\VarDumper\VarDumper;

/**
 * @param mixed $var
 *
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
function d($var) {
    foreach (func_get_args() as $var) {
        VarDumper::dump($var);
    }
}
