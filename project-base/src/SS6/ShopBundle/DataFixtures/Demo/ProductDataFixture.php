<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductDataFixture extends AbstractFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$productData = new ProductData();
		$productData->setName('LG 42LN577S');
		$productData->setCatnum('LG 42LN577S');
		$productData->setPartno('ABC101');
		$productData->setEan('889977446655');
		$productData->setDescription('Přivítejte nový tablet Note 8.0, vašeho každodenního společníka na cestě životem. Nabízí výborné připojení a hbitý výkon, je vybavený perem S Pen s přirozeným držením a má perfektní rozměry k tomu, aby mohl být vždy s vámi a okamžitě po ruce. Dokonalá velikost na cesty.');
		$productData->setPrice('11790');
		$productData->setVat($this->getReference(VatDataFixture::VAT_HIGH));
		$productData->setStockQuantity(10);
		$productData->setHidden(false);
		$productData->setAvailability($this->getReference(AvailabilityDataFixture::IN_STOCK));
		$this->createProduct($manager, 'product_1', $productData);

		$productData->setName('Samsung Galaxy Note 8');
		$productData->setCatnum('ABC102');
		$productData->setPartno('SGN-8');
		$productData->setEan('888994466122');
		$productData->setDescription('Přivítejte nový tablet Note 8.0, vašeho každodenního společníka na cestě životem. Nabízí výborné připojení a hbitý výkon, je vybavený perem S Pen s přirozeným držením a má perfektní rozměry k tomu, aby mohl být vždy s vámi a okamžitě po ruce. Dokonalá velikost na cesty.');
		$productData->setPrice('7999');
		$productData->setStockQuantity(3);
		$productData->setAvailability($this->getReference(AvailabilityDataFixture::OUT_OF_STOCK));
		$this->createProduct($manager, 'product_2', $productData);

		$productData->setName('Lenovo Yoga 10');
		$productData->setCatnum('ABC103');
		$productData->setPartno('LEN-Y-10');
		$productData->setEan(null);
		$productData->setDescription('Model Yoga Tablet 10, stříbrný. Inovativní vícerežimový design. Přelomový design Yoga Tablet 10 je ozvláštněn komorou s baterií, čímž došlo k posunu těžiště a tím i k mnohem lepšímu držení tabletu. Přidaný stojan na zadní straně zařízení umožňuje používat tablet ve třech režimech: Podrž, Postav, Polož. Při držení tabletu v ruce leží váha zařízení ve vaší dlani, nikoliv na prstech, díky čemuž je čtení nebo prohlížení webu pohodlnější a přirozenější.');
		$productData->setPrice('6999');
		$productData->setSellingFrom(new DateTime('2020-01-01'));
		$productData->setStockQuantity(1);
		$productData->setHidden(true);
		$this->createProduct($manager, 'product_3', $productData);

		$productData->setName('Nokia 5110');
		$productData->setCatnum('ABC104');
		$productData->setPartno('N5100');
		$productData->setEan('846544987564');
		$productData->setDescription('Na trh byl uveden v roce 1998. Tento mobilní telefon je odlehčenou verzí modelu Nokia 6110, který byl koncipován jako manažerský. V USA se prodávala jeho varianta Nokia 5190 a existovaly i další varianty pro různé trhy. Telefon má fyzické rozměry 48 × 132 × 31 mm, jeho hmotnost s originální baterií a bez SIM karty je 170 g. Telefon jako jeden z prvních přinesl přední výměnný kryt a vestavěné hry. Nevýhoda ale byla absence podpory novějšího GSM pásma o frekvenci 1800 MHz.');
		$productData->setPrice('3599');
		$productData->setSellingFrom(new DateTime('1998-01-01'));
		$productData->setSellingFrom(new DateTime('2001-12-31'));
		$productData->setStockQuantity(0);
		$this->createProduct($manager, 'product_4', $productData);

		$productData->setName('Koncept');
		$productData->setCatnum(null);
		$productData->setPartno(null);
		$productData->setEan(null);
		$productData->setDescription('Ještě nevím');
		$productData->setPrice(null);
		$productData->setSellingFrom(null);
		$productData->setSellingFrom(null);
		$productData->setStockQuantity(null);
		$productData->setVat($this->getReference(VatDataFixture::VAT_ZERO));
		$this->createProduct($manager, 'product_5', $productData);
		// @codingStandardsIgnoreStop

		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 */
	private function createProduct(ObjectManager $manager, $referenceName, ProductData $productData) {
		$product = new Product($productData);

		$manager->persist($product);
		$this->addReference($referenceName, $product);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			VatDataFixture::class,
			AvailabilityDataFixture::class,
		);
	}

}
