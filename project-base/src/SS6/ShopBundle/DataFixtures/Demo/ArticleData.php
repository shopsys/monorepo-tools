<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Article\Article;

class ArticleData extends AbstractFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$this->createArticle($manager, 'Nový ShopSys 6', "Vychází nová verze ShopSys.\nJiž šestá verze tohoto úspěšného produktu je jednou s nejvyspělejších platforem pro tvorbu internetových obchodů.");
		$this->createArticle($manager, 'Obama encountering growing election-year dissent from Democrats', "President Barack Obama is encountering an increasingly resistant Democratic caucus on Capitol Hill, as lawmakers in his party break with him on a series of issues in the run-up to the November elections.\nOn issues such as judicial nominees, the Keystone XL pipeline, taxes and trade, the fraying party unity is a sign that individual Democrats have reached a point where their own re-election needs take precedence over Mr. Obama's goals.");
		$this->createArticle($manager, 'NASA Discovers New Evidence to Suggest Water On Mars', "While this may provide some evidence of life on Mars, the authors note \"that they cannot exclude the possibility that the carbon-rich regions in both sets of features may be the product of [other biological reasons].\n\"This is no smoking gun,\" said Lauren White, lead author of the study to NASA. \"We can never eliminate the possibility of contamination in any meteorite. But these features are nonetheless interesting and show that further studies of these meteorites should continue.\"");
		// @codingStandardsIgnoreStop

		$manager->flush();
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $name
	 * @param string|null $text
	 */
	private function createArticle(ObjectManager $manager, $name, $text = null) {
		$product = new Article($name, $text);

		$manager->persist($product);
	}

}
