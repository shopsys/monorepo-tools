<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin;

use Facebook\WebDriver\WebDriverBy;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class ProductAdvancedSearchPage extends AbstractPage
{

    const SEARCH_SUBJECT_CATNUM = 'productCatnum';

    /**
     * @param string $searchSubject
     * @param string $value
     */
    public function search($searchSubject, $value) {
        $this->tester->amOnPage('/admin/product/list/');

        $this->tester->clickByText('Rozšířené hledání');
        $this->tester->selectOptionByCssAndValue('.js-advanced-search-rule-subject', $searchSubject);
        $this->tester->waitForAjax();
        $this->tester->fillFieldByCss('.js-advanced-search-rule-value input', $value);

        $this->tester->clickByText('Vyhledat', WebDriverBy::cssSelector('#js-advanced-search-rules-box'));
    }

    /**
     * @param string $productName
     */
    public function assertFoundProductByName($productName) {
        $this->tester->seeInCss($productName, '.js-grid-column-name');
    }

    /**
     * @param int $expectedCount
     */
    public function assertFoundProductCount($expectedCount) {
        $foundProductCount = $this->tester->countVisibleByCss('tbody .table-grid__row');

        if ($foundProductCount !== $expectedCount) {
            $message = 'Product advanced search expected to found ' . $expectedCount . ' products but found ' . $foundProductCount . '.';
            throw new \PHPUnit_Framework_ExpectationFailedException($message);
        }
    }

}
