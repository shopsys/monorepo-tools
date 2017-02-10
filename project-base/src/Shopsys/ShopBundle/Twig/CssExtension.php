<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Component\Css\CssFacade;
use Twig_SimpleFunction;

class CssExtension extends \Twig_Extension
{

    /**
     * @var \Shopsys\ShopBundle\Component\Css\CssFacade
     */
    private $cssFacade;

    public function __construct(CssFacade $cssFacade) {
        $this->cssFacade = $cssFacade;
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new Twig_SimpleFunction('getCssVersion', [$this, 'getCssVersion']),
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        return 'css';
    }

    /**
     * @return string
     */
    public function getCssVersion() {
        return $this->cssFacade->getCssVersion();
    }

}
