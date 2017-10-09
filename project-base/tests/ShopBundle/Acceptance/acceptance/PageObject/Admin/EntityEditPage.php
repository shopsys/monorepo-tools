<?php

namespace Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin;

use Facebook\WebDriver\WebDriverBy;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\AbstractPage;

class EntityEditPage extends AbstractPage
{
    const TEST_IMAGE_NAME = 'test.png';

    /**
     * @param string $imageUploadFieldId
     */
    public function uploadTestImage($imageUploadFieldId)
    {
        $imageUploadInput = $this->webDriver->findElement(WebDriverBy::id($imageUploadFieldId));

        $this->tester->attachFile($imageUploadInput, self::TEST_IMAGE_NAME);
        $this->tester->waitForAjax();
    }
}
