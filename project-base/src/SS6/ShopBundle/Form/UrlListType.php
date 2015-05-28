<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UrlListType extends AbstractType {

	const TO_DELETE = 'toDelete';
	const MAIN_ON_DOMAINS = 'mainOnDomains';

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		FriendlyUrlFacade $friendlyUrlFacade,
		DomainRouterFactory $domainRouterFactory,
		Domain $domain
	) {
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->domain = $domain;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		if ($options['route_name'] === null) {
			throw new \SS6\ShopBundle\Form\Exception\MissingRouteNameException();
		}

		$builder->add(self::TO_DELETE, FormType::FORM);
		$builder->add(self::MAIN_ON_DOMAINS, FormType::FORM);

		$friendlyUrlsByDomain = $this->getFriendlyUrlsIndexedByDomain($options['route_name'], $options['entity_id']);

		foreach ($friendlyUrlsByDomain as $domainId => $friendlyUrls) {
			$builder->get(self::TO_DELETE)->add($domainId, FormType::CHOICE, [
				'required' => false,
				'multiple' => true,
				'expanded' => true,
				'choice_list' => new ObjectChoiceList($friendlyUrls, 'slug', [], null, 'slug'),
			]);
			$builder->get(self::MAIN_ON_DOMAINS)->add($domainId, FormType::CHOICE, [
				'required' => true,
				'multiple' => false,
				'expanded' => true,
				'choice_list' => new ObjectChoiceList($friendlyUrls, 'slug', [], null, 'slug'),
				'invalid_message' => 'Původně vybraná hlavní URL již neexistuje',
			]);
		}
	}

	/**
	 * @param \Symfony\Component\Form\FormView $view
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @param array $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options) {
		$absoluteUrlsByDomainIdAndSlug = $this->getAbsoluteUrlsIndexedByDomainIdAndSlug(
			$options['route_name'],
			$options['entity_id']
		);
		$mainUrlsSlugsOnDomains = $this->getMainFriendlyUrlSlugsByDomainId(
			$options['route_name'],
			$options['entity_id']
		);

		$view->vars['absoluteUrlsByDomainIdAndSlug'] = $absoluteUrlsByDomainIdAndSlug;
		$view->vars['routeName'] = $options['route_name'];
		$view->vars['entityId'] = $options['entity_id'];
		$view->vars['mainUrlsSlugsOnDomains'] = $mainUrlsSlugsOnDomains;
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'required' => false,
			'route_name' => null,
			'entity_id' => null,
		]);
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'form';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'url_list';
	}

	/**
	 * @param string $routeName
	 * @param string $entityId
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[domainId][]
	 */
	private function getFriendlyUrlsIndexedByDomain($routeName, $entityId) {
		$friendlyUrlsByDomain = [];
		if ($entityId !== null) {
			$friendlyUrls = $this->friendlyUrlFacade->getAllByRouteNameAndEntityId($routeName, $entityId);
			foreach ($friendlyUrls as $friendlyUrl) {
				$friendlyUrlsByDomain[$friendlyUrl->getDomainId()][] = $friendlyUrl;
			}
		}

		return $friendlyUrlsByDomain;
	}

	/**
	 * @param string $routeName
	 * @param string $entityId
	 * @return string[domainId][slug]
	 */
	private function getAbsoluteUrlsIndexedByDomainIdAndSlug($routeName, $entityId) {
		$friendlyUrlsByDomain = $this->getFriendlyUrlsIndexedByDomain($routeName, $entityId);
		$absoluteUrlsByDomainIdAndSlug = [];
		foreach ($friendlyUrlsByDomain as $domainId => $friendlyUrls) {
			$domainRouter = $this->domainRouterFactory->getRouter($domainId);
			$absoluteUrlsByDomainIdAndSlug[$domainId] = [];
			foreach ($friendlyUrls as $friendlyUrl) {
				$absoluteUrlsByDomainIdAndSlug[$domainId][$friendlyUrl->getSlug()] =
					$domainRouter->generateByFriendlyUrl(
						$friendlyUrl,
						[],
						Router::ABSOLUTE_URL
					);
			}
		}

		return $absoluteUrlsByDomainIdAndSlug;
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @return string[domainId]
	 */
	private function getMainFriendlyUrlSlugsByDomainId($routeName, $entityId) {
		$mainFriendlyUrlsSlugsOnDomains = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$domainId = $domainConfig->getId();
			$mainFriendlyUrl = $this->friendlyUrlFacade->findMainFriendlyUrl(
				$domainId,
				$routeName,
				$entityId
			);
			if ($mainFriendlyUrl !== null) {
				$mainFriendlyUrlsSlugsOnDomains[$domainId] = $mainFriendlyUrl->getSlug();
			} else {
				$mainFriendlyUrlsSlugsOnDomains[$domainId] = null;
			}
		}

		return $mainFriendlyUrlsSlugsOnDomains;
	}

}