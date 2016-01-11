<?php

namespace SS6\ShopBundle\Model\Feed;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface;
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
	 * @param \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface $feedItemIteratorFactory
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string $feedTemplatePath
	 * @param string $targetFilepath
	 */
	public function generate(
		FeedItemIteratorFactoryInterface $feedItemIteratorFactory,
		DomainConfig $domainConfig,
		$feedTemplatePath,
		$targetFilepath
	) {
		set_time_limit(0);
		$twigTemplate = $this->twig->loadTemplate($feedTemplatePath);
		file_put_contents($targetFilepath, $this->getRenderedBlock($twigTemplate, 'begin'));

		$feedItemIterator = $feedItemIteratorFactory->getIterator($domainConfig);

		$this->generateItems($twigTemplate, $targetFilepath, $feedItemIterator, false);

		file_put_contents($targetFilepath, $this->getRenderedBlock($twigTemplate, 'end'), FILE_APPEND);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface $feedItemIteratorFactory
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string $feedTemplatePath
	 * @param string $targetFilepath
	 * @param int|null $feedItemIdToContinue
	 * @return \SS6\ShopBundle\Model\Feed\FeedItemInterface|null
	 */
	public function generateIteratively(
		FeedItemIteratorFactoryInterface $feedItemIteratorFactory,
		DomainConfig $domainConfig,
		$feedTemplatePath,
		$targetFilepath,
		$feedItemIdToContinue
	) {
		$twigTemplate = $this->twig->loadTemplate($feedTemplatePath);
		if ($feedItemIdToContinue === null) {
			file_put_contents($targetFilepath, $this->getRenderedBlock($twigTemplate, 'begin'));
		}

		$feedItemIterator = $feedItemIteratorFactory->getIterator($domainConfig);
		$feedItemIterator->setFeedItemIdToContinue($feedItemIdToContinue);

		$this->generateItems($twigTemplate, $targetFilepath, $feedItemIterator, true);
		$feedItemToContinue = $feedItemIterator->current();
		if ($feedItemToContinue === false) {
			file_put_contents($targetFilepath, $this->getRenderedBlock($twigTemplate, 'end'), FILE_APPEND);

			return null;
		} else {
			return $feedItemToContinue;
		}
	}

	/**
	 * @param \Twig_Template $twigTemplate
	 * @param string $targetFilepath
	 * @param \SS6\ShopBundle\Model\Feed\FeedItemInterface[] $feedItemIterator
	 * @param bool $iteratively
	 */
	private function generateItems(
		Twig_Template $twigTemplate,
		$targetFilepath,
		$feedItemIterator,
		$iteratively
	) {
		$buffer = '';
		$counter = 0;
		foreach ($feedItemIterator as $feedItem) {
			$counter++;
			$buffer .= $this->getRenderedBlock($twigTemplate, 'item', ['item' => $feedItem]);
			if ($counter >= self::BATCH_SIZE) {
				file_put_contents($targetFilepath, $buffer, FILE_APPEND);
				$buffer = '';
				$counter = 0;
				$this->em->clear();
				if ($iteratively) {
					return;
				}
			}
		}

		if ($counter > 0) {
			file_put_contents($targetFilepath, $buffer, FILE_APPEND);
			$this->em->clear();
		}

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

		throw new \SS6\ShopBundle\Model\Feed\Exception\TemplateBlockNotFoundException($name, $twigTemplate->getTemplateName());
	}

}
