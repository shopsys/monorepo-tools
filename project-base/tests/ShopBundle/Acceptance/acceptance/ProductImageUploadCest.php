<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\EntityEditPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class ProductImageUploadCest
{
    const IMAGE_UPLOAD_FIELD_ID = 'product_edit_form_imagesToUpload_file';
    const SAVE_BUTTON_NAME = 'product_edit_form[save]';

    const EXPECTED_SUCCESS_MESSAGE = 'Product 22" Sencor SLE 22F46DM4 HELLO KITTY modified';

    public function testSuccessfulImageUpload(AcceptanceTester $me, EntityEditPage $entityEditPage, LoginPage $loginPage)
    {
        $me->wantTo('upload image in admin product edit page');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/product/edit/1');
        $entityEditPage->uploadTestImage(self::IMAGE_UPLOAD_FIELD_ID);
        $me->clickByName(self::SAVE_BUTTON_NAME);
        $me->see(self::EXPECTED_SUCCESS_MESSAGE);
    }
}
