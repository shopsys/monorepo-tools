<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\InlineEditPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class PromoCodeInlineEditCest
{
    /**
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage $loginPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\InlineEditPage $inlineEditPage
     */
    public function testPromoCodeEdit(AcceptanceTester $me, LoginPage $loginPage, InlineEditPage $inlineEditPage)
    {
        $me->wantTo('promo code can be edited via inline edit');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/promo-code/list');

        $inlineEditPage->startInlineEdit(1);
        $inlineEditPage->changeInputValue(1, 'code', 'test edited');
        $inlineEditPage->save(1);

        $inlineEditPage->assertSeeInColumn(1, 'code', 'test edited');
    }

    /**
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage $loginPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\InlineEditPage $inlineEditPage
     */
    public function testPromoCodeCreate(AcceptanceTester $me, LoginPage $loginPage, InlineEditPage $inlineEditPage)
    {
        $me->wantTo('promo code can be created via inline edit');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/promo-code/list');

        $inlineEditPage->createNewRow();
        $inlineEditPage->changeInputValue(null, 'code', 'test created');
        $inlineEditPage->changeInputValue(null, 'percent', '5');
        $inlineEditPage->save(null);

        $newRowId = $inlineEditPage->getHighestRowId();

        $inlineEditPage->assertSeeInColumn($newRowId, 'code', 'test created');
        $inlineEditPage->assertSeeInColumn($newRowId, 'percent', '5%');
    }

    /**
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage $loginPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\InlineEditPage $inlineEditPage
     */
    public function testPromoCodeDelete(AcceptanceTester $me, LoginPage $loginPage, InlineEditPage $inlineEditPage)
    {
        $me->wantTo('promo code can be deleted via inline edit');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/promo-code/list');

        $inlineEditPage->delete(1);

        $inlineEditPage->assertDontSeeRow(1);
        $me->see('Promo code test deleted.');
    }
}
