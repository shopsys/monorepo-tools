<?php

namespace Tests\ShopBundle\Acceptance\acceptance\PageObject\Front;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\AbstractPage;

class CartBoxPage extends AbstractPage
{
    /**
     * @param string $text
     */
    public function seeInCartBox($text)
    {
        $this->tester->seeInCss($text, '.js-cart-info');
    }
}
