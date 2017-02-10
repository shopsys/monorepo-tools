<?php

namespace Shopsys\ShopBundle\Component\Error;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseController;

class ExceptionController extends BaseController
{

    /**
     * @var bool
     */
    private $showErrorPagePrototype = false;

    /**
     * @param bool $bool
     */
    public function setDebug($bool) {
        $this->debug = $bool;
    }

    /**
     * @return bool
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function isShownErrorPagePrototype() {
        return $this->showErrorPagePrototype;
    }

    public function setShowErrorPagePrototype() {
        $this->showErrorPagePrototype = true;
    }
}
