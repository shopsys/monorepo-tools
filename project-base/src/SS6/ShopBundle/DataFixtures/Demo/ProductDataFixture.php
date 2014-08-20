<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductDataFixture extends AbstractFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$this->createProduct($manager, 'product_1', new ProductData('LG 42LN577S', 'ABC101', 'LG123456', '889977446655', 'Přivítejte nový tablet Note 8.0, vašeho každodenního společníka na cestě životem. Nabízí výborné připojení a hbitý výkon, je vybavený perem S Pen s přirozeným držením a má perfektní rozměry k tomu, aby mohl být vždy s vámi a okamžitě po ruce. Dokonalá velikost na cesty.', '11790', $this->getReference('vat_high'), null, null , 10, false));
		$this->createProduct($manager, 'product_2', new ProductData('Samsung Galaxy Note 8', 'ABC102', 'SGN-8', '888994466122', 'Přivítejte nový tablet Note 8.0, vašeho každodenního společníka na cestě životem. Nabízí výborné připojení a hbitý výkon, je vybavený perem S Pen s přirozeným držením a má perfektní rozměry k tomu, aby mohl být vždy s vámi a okamžitě po ruce. Dokonalá velikost na cesty.', '7990', $this->getReference('vat_high'), null, null, 3, false));
		$this->createProduct($manager, 'product_3', new ProductData('Lenovo Yoga 10', 'ABC103', 'LEN-Y-10', null, 'Model Yoga Tablet 10, stříbrný. Inovativní vícerežimový design. Přelomový design Yoga Tablet 10 je ozvláštněn komorou s baterií, čímž došlo k posunu těžiště a tím i k mnohem lepšímu držení tabletu. Přidaný stojan na zadní straně zařízení umožňuje používat tablet ve třech režimech: Podrž, Postav, Polož. Při držení tabletu v ruce leží váha zařízení ve vaší dlani, nikoliv na prstech, díky čemuž je čtení nebo prohlížení webu pohodlnější a přirozenější.', '6999', $this->getReference('vat_high'), new DateTime('2020-01-01'), null, 1, false));
		$this->createProduct($manager, 'product_4', new ProductData('Nokia 5110', 'ABC104', 'N5100', '846544987564', 'Na trh byl uveden v roce 1998. Tento mobilní telefon je odlehčenou verzí modelu Nokia 6110, který byl koncipován jako manažerský. V USA se prodávala jeho varianta Nokia 5190 a existovaly i další varianty pro různé trhy. Telefon má fyzické rozměry 48 × 132 × 31 mm, jeho hmotnost s originální baterií a bez SIM karty je 170 g. Telefon jako jeden z prvních přinesl přední výměnný kryt a vestavěné hry. Nevýhoda ale byla absence podpory novějšího GSM pásma o frekvenci 1800 MHz.', '3599', $this->getReference('vat_low'), new DateTime('1998-01-01'), new DateTime('2001-12-31'), 0, false));
		$this->createProduct($manager, 'product_5', new ProductData('Koncept', null, null, null, 'Ještě nevím.', null, $this->getReference('vat_zero'), null, null, null, true));
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
			VatData::class,
		);
	}

}
