<?php

namespace Tests\ShopBundle\Acceptance\acceptance\PageObject\Front;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\AbstractPage;

class LayoutPage extends AbstractPage
{
    /**
     * @param string $email
     * @param string $password
     */
    public function openLoginPopup()
    {
        $this->tester->clickByText('Log in');
        $this->tester->wait(1); // wait for Shopsys.window to show
    }
}
