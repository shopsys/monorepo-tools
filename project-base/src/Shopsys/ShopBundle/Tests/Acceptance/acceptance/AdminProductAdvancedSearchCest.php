<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\LoginPage;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\ProductAdvancedSearchPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class AdminProductAdvancedSearchCest {

	public function testSearchByCatnum(
		AcceptanceTester $me,
		LoginPage $loginPage,
		ProductAdvancedSearchPage $productAdvancedSearchPage
	) {
		$me->wantTo('search for product by catnum');
		$loginPage->login(LoginPage::ADMIN_USERNAME, LoginPage::ADMIN_PASSWORD);

		$productAdvancedSearchPage->search(ProductAdvancedSearchPage::SEARCH_SUBJECT_CATNUM, '9176544MG');

		$productAdvancedSearchPage->assertFoundProductByName('Aquila Aquagym Pramenitá voda neperlivá');
		$productAdvancedSearchPage->assertFoundProductCount(1);
	}

}
