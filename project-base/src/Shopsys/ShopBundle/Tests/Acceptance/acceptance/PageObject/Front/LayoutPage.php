<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class LayoutPage extends AbstractPage
{

    /**
     * @param string $email
     * @param string $password
     */
    public function openLoginPopup() {
        $this->tester->clickByText('Přihlásit se');
        $this->tester->wait(1); // wait for Shopsys.window to show
    }

}
