<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class CartBoxPage extends AbstractPage
{

    /**
     * @param string $text
     */
    public function seeInCartBox($text) {
        $this->tester->seeInCss($text, '.js-cart-info');
    }

}
