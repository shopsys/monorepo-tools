<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Route;

class FriendlyUrlGeneratorFacade {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlToGenerateRepository
	 */
	private $friendlyUrlToGenerateRepository;

	public function __construct(
		Domain $domain,
		DomainRouterFactory $domainRouterFactory,
		FriendlyUrlFacade $friendlyUrlFacade,
		FriendlyUrlToGenerateRepository $friendlyUrlToGenerateRepository
	) {
		$this->domain = $domain;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->friendlyUrlToGenerateRepository = $friendlyUrlToGenerateRepository;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	public function generateUrlsForSupportedEntities(OutputInterface $output) {
		foreach ($this->domain->getAll() as $domainConfig) {
			$output->writeln(' Start of generating friendly urls for domain ' . $domainConfig->getUrl() . '');

			$countOfCreatedUrls = $this->generateUrlsByDomainConfig($output, $domainConfig);

			$output->writeln(sprintf(
				' End of generating friendly urls for domain %s (%d).',
				$domainConfig->getUrl(),
				$countOfCreatedUrls
			));
		}
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return int
	 */
	private function generateUrlsByDomainConfig(OutputInterface $output, DomainConfig $domainConfig) {
		$totalCountOfCreatedUrls = 0;
		$friendlyUrlRouter = $this->domainRouterFactory->getFriendlyUrlRouter($domainConfig);

		foreach ($friendlyUrlRouter->getRouteCollection() as $routeName => $route) {
			$countOfCreatedUrls = $this->generateUrlsByRoute($domainConfig, $routeName);
			$totalCountOfCreatedUrls += $countOfCreatedUrls;

			$output->writeln(sprintf(
				'   -> route %s in %s (%d)',
				$routeName,
				$route->getDefault('_controller'),
				$countOfCreatedUrls
			));
		}

		return $totalCountOfCreatedUrls;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string $routeName
	 * @return int
	 */
	private function generateUrlsByRoute(DomainConfig $domainConfig, $routeName) {
		$countOfCreatedUrls = 0;

		foreach ($this->getFriendlyUrlsDataByRouteName($routeName, $domainConfig) as $friendlyUrlData) {
			$this->friendlyUrlFacade->createFriendlyUrlForDomain(
				$routeName,
				$friendlyUrlData->id,
				$friendlyUrlData->name,
				$domainConfig->getId()
			);
			$countOfCreatedUrls++;
		}

		return $countOfCreatedUrls;
	}

	/**
	 * @param string $routeName
	 * @param DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
	 */
	private function getFriendlyUrlsDataByRouteName($routeName, DomainConfig $domainConfig) {
		switch ($routeName) {
			case 'front_article_detail':
				return $this->friendlyUrlToGenerateRepository->getArticleData($routeName, $domainConfig);

			case 'front_product_detail':
				return $this->friendlyUrlToGenerateRepository->getProductDetailData($routeName, $domainConfig);

			case 'front_product_list':
				return $this->friendlyUrlToGenerateRepository->getProductListData($routeName, $domainConfig);
		}

		throw new \SS6\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlRouteNotSupportedException($routeName);
	}

}
