<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\EntityEditPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class PaymentImageUploadCest
{
    const IMAGE_UPLOAD_FIELD_ID = 'payment_edit_form_paymentData_image_file';
    const SAVE_BUTTON_NAME = 'payment_edit_form[save]';

    const EXPECTED_SUCCESS_MESSAGE = 'Payment Credit card modified';

    public function testSuccessfulImageUpload(AcceptanceTester $me, EntityEditPage $entityEditPage, LoginPage $loginPage)
    {
        $me->wantTo('Upload an image in admin payment edit page');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/payment/edit/1');
        $entityEditPage->uploadTestImage(self::IMAGE_UPLOAD_FIELD_ID);
        $me->clickByName(self::SAVE_BUTTON_NAME);
        $me->see(self::EXPECTED_SUCCESS_MESSAGE);
    }
}
