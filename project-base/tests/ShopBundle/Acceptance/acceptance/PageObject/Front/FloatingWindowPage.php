<?php

namespace Tests\ShopBundle\Acceptance\acceptance\PageObject\Front;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\AbstractPage;

class FloatingWindowPage extends AbstractPage
{
    public function closeFloatingWindow()
    {
        $this->tester->clickByCss('.js-window-button-close');
        $this->tester->wait(1); // animation of closing sometime hides page content
    }
}
