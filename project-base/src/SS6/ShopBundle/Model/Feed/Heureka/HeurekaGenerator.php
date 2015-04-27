<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedDataSourceInterface;
use SS6\ShopBundle\Model\Feed\FeedGeneratorInterface;
use Twig_Environment;

class HeurekaGenerator implements FeedGeneratorInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	private $heurekaDataSource;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Twig_Template
	 */
	private $twigTemplate;

	public function __construct(FeedDataSourceInterface $heurekaDataSource, Twig_Environment $twig, EntityManager $em) {
		$this->heurekaDataSource = $heurekaDataSource;
		$this->twig = $twig;
		$this->twigTemplate = $this->twig->loadTemplate('@SS6Shop/Feed/heureka.xml.twig');
		$this->em = $em;
	}

		/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param string $targetFilepath
	 */
	public function generate(DomainConfig $domainConfig, $targetFilepath) {
		set_time_limit(0);
		file_put_contents($targetFilepath, $this->getRenderedBlock('begin'));

		$xmlItems = '';
		$counter = 0;
		foreach ($this->heurekaDataSource->getIterator($domainConfig) as $feedItem) {
			$counter++;
			$xmlItems .= $this->getRenderedBlock('item', ['item' => $feedItem]);
			if ($counter >= 100) {
				file_put_contents($targetFilepath, $xmlItems, FILE_APPEND);
				$xmlItems = '';
				$counter = 0;
				$this->em->clear();
			}
		}

		file_put_contents($targetFilepath, $this->getRenderedBlock('end'), FILE_APPEND);
	}

	/**
	 * @param string $name
	 * @param array $parameters
	 * @param bool $echo
	 */
	public function getRenderedBlock($name, array $parameters = []) {
		if ($this->twigTemplate->hasBlock($name)) {
			$templateParameters = array_merge(
				$this->twig->getGlobals(),
				$parameters
			);
			return $this->twigTemplate->renderBlock($name, $templateParameters);
		}

		throw new \SS6\ShopBundle\Model\Feed\TemplateBlockNotFoundException($name, '@SS6Shop/Feed/heureka.xml.twig');
	}

}
