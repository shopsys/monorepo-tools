<?php

namespace Shopsys\ShopBundle\Component\System;

class System
{

    /**
     * @return bool
     */
    public function isWindows() {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * @return bool
     */
    public function isMac() {
        return stripos(PHP_OS, 'darwin') === 0;
    }
}
