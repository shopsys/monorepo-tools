<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\EntityEditPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class ProductImageUploadCest
{
    protected const IMAGE_UPLOAD_FIELD_ID = 'product_form_imageGroup_images_file';
    protected const SAVE_BUTTON_NAME = 'product_form[save]';

    protected const EXPECTED_SUCCESS_MESSAGE = 'Product 22" Sencor SLE 22F46DM4 HELLO KITTY modified';

    protected const TEST_IMAGE_NAME = 'productTestImage.png';

    /**
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\EntityEditPage $entityEditPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage $loginPage
     */
    public function testSuccessfulImageUpload(AcceptanceTester $me, EntityEditPage $entityEditPage, LoginPage $loginPage)
    {
        $me->wantTo('upload image in admin product edit page');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/product/edit/1');
        $entityEditPage->uploadTestImage(self::IMAGE_UPLOAD_FIELD_ID, self::TEST_IMAGE_NAME);
        $me->clickByName(self::SAVE_BUTTON_NAME);
        $me->see(self::EXPECTED_SUCCESS_MESSAGE);
    }
}
