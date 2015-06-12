<?php

namespace SS6\ShopBundle\Model\Feed;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedDataSourceInterface;
use Twig_Environment;
use Twig_Template;

class FeedGenerator {

	const BATCH_SIZE = 100;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(Twig_Environment $twig, EntityManager $em) {
		$this->twig = $twig;
		$this->em = $em;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param string $targetFilepath
	 */
	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface $heurekaDataSource
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param string $feedTemplatePath
	 * @param string $targetFilepath
	 */
	public function generate(
		FeedDataSourceInterface $heurekaDataSource,
		DomainConfig $domainConfig,
		$feedTemplatePath,
		$targetFilepath
	) {
		set_time_limit(0);
		$twigTemplate = $this->twig->loadTemplate($feedTemplatePath);
		file_put_contents($targetFilepath, $this->getRenderedBlock($twigTemplate, 'begin'));

		$buffer = '';
		$counter = 0;
		foreach ($heurekaDataSource->getIterator($domainConfig) as $feedItem) {
			$counter++;
			$buffer .= $this->getRenderedBlock($twigTemplate, 'item', ['item' => $feedItem]);
			if ($counter >= self::BATCH_SIZE) {
				file_put_contents($targetFilepath, $buffer, FILE_APPEND);
				$buffer = '';
				$counter = 0;
				$this->em->clear();
			}
		}

		if ($counter > 0) {
			file_put_contents($targetFilepath, $buffer, FILE_APPEND);
			$this->em->clear();
		}

		file_put_contents($targetFilepath, $this->getRenderedBlock($twigTemplate, 'end'), FILE_APPEND);
	}

	/**
	 * @param \Twig_Template $twigTemplate
	 * @param string $name
	 * @param array $parameters
	 */
	private function getRenderedBlock(Twig_Template $twigTemplate, $name, array $parameters = []) {
		if ($twigTemplate->hasBlock($name)) {
			$templateParameters = array_merge(
				$this->twig->getGlobals(),
				$parameters
			);
			return $twigTemplate->renderBlock($name, $templateParameters);
		}

		throw new \SS6\ShopBundle\Model\Feed\TemplateBlockNotFoundException($name, $twigTemplate->getTemplateName());
	}

}
