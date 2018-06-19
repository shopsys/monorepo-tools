<?php

namespace Tests\ShopBundle\Smoke;

use Shopsys\FrameworkBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\FrameworkBundle\Form\Admin\Product\ProductEditFormType;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Security\Csrf\CsrfToken;
use Tests\ShopBundle\Test\FunctionalTestCase;

class NewProductTest extends FunctionalTestCase
{
    public function createOrEditProductProvider()
    {
        return [['admin/product/new/'], ['admin/product/edit/1']];
    }

    /**
     * @dataProvider createOrEditProductProvider
     */
    public function testCreateOrEditProduct($relativeUrl)
    {
        $client1 = $this->getClient(false, 'admin', 'admin123');
        $crawler = $client1->request('GET', $relativeUrl);

        $form = $crawler->filter('form[name=product_edit_form]')->form();
        $this->fillForm($form);

        $client2 = $this->getClient(true, 'admin', 'admin123');
        $em2 = $client2->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $em2 \Doctrine\ORM\EntityManager */

        $em2->beginTransaction();

        $tokenManager = $client2->getContainer()->get('security.csrf.token_manager');
        /* @var $tokenManager \Symfony\Component\Security\Csrf\CsrfTokenManager */
        $token = $tokenManager->getToken(ProductEditFormType::CSRF_TOKEN_ID);
        $this->setFormCsrfToken($form, $token);

        $client2->submit($form);

        $em2->rollback();

        $flashMessageBag = $client2->getContainer()->get('shopsys.shop.component.flash_message.bag.admin');
        /* @var $flashMessageBag \Shopsys\FrameworkBundle\Component\FlashMessage\Bag */

        $this->assertSame(302, $client2->getResponse()->getStatusCode());
        $this->assertNotEmpty($flashMessageBag->getSuccessMessages());
        $this->assertEmpty($flashMessageBag->getErrorMessages());
    }

    /**
     * @param \Symfony\Component\DomCrawler\Form $form
     */
    private function fillForm(Form $form)
    {
        $nameForms = $form->get('product_edit_form[productData][name]');
        /* @var $nameForms \Symfony\Component\DomCrawler\Field\InputFormField[] */
        foreach ($nameForms as $nameForm) {
            $nameForm->setValue('testProduct');
        }
        $form['product_edit_form[productData][catnum]'] = '123456';
        $form['product_edit_form[productData][partno]'] = '123456';
        $form['product_edit_form[productData][ean]'] = '123456';
        $form['product_edit_form[productData][descriptions][1]'] = 'test description';
        $form['product_edit_form[productData][price]'] = '10000';
        $form['product_edit_form[productData][vat]']->select($this->getReference(VatDataFixture::VAT_ZERO)->getId());
        $form['product_edit_form[productData][sellingFrom]'] = '1.1.1990';
        $form['product_edit_form[productData][sellingTo]'] = '1.1.2000';
        $form['product_edit_form[productData][stockQuantity]'] = '10';
        $form['product_edit_form[productData][unit]']->select($this->getReference(UnitDataFixture::UNIT_CUBIC_METERS)->getId());
        $form['product_edit_form[productData][availability]']->select($this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK)->getId());
    }

    /**
     * @param \Symfony\Component\DomCrawler\Form $form
     * @param \Symfony\Component\Security\Csrf\CsrfToken $token
     */
    private function setFormCsrfToken(Form $form, CsrfToken $token)
    {
        $form['product_edit_form[_token]'] = $token->getValue();
    }
}
