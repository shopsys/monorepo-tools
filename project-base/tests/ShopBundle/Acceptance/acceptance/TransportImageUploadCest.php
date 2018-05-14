<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\EntityEditPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class TransportImageUploadCest
{
    const IMAGE_UPLOAD_FIELD_ID = 'transport_edit_form_transportData_image_image_file';
    const SAVE_BUTTON_NAME = 'transport_edit_form[save]';

    const EXPECTED_SUCCESS_MESSAGE = 'Shipping Czech post was modified';

    public function testSuccessfulImageUpload(AcceptanceTester $me, EntityEditPage $entityEditPage, LoginPage $loginPage)
    {
        $me->wantTo('Upload an image in admin transport edit page');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/transport/edit/1');
        $entityEditPage->uploadTestImage(self::IMAGE_UPLOAD_FIELD_ID);
        $me->clickByName(self::SAVE_BUTTON_NAME);
        $me->see(self::EXPECTED_SUCCESS_MESSAGE);
    }
}
