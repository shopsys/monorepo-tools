<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\ProductAdvancedSearchPage;
use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class AdminProductAdvancedSearchCest
{
    public function testSearchByCatnum(
        AcceptanceTester $me,
        LoginPage $loginPage,
        ProductAdvancedSearchPage $productAdvancedSearchPage
    ) {
        $me->wantTo('search for product by catnum');
        $loginPage->login(LoginPage::ADMIN_USERNAME, LoginPage::ADMIN_PASSWORD);

        $productAdvancedSearchPage->search(ProductAdvancedSearchPage::SEARCH_SUBJECT_CATNUM, '9176544MG');

        $productAdvancedSearchPage->assertFoundProductByName('Aquila Aquagym non-carbonated spring water');
        $productAdvancedSearchPage->assertFoundProductCount(1);
    }
}
