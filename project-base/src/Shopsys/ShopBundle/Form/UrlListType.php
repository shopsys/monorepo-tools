<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Constraints\UniqueSlugsOnDomains;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\ShopBundle\Form\UrlListData;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UrlListType extends AbstractType {

	const TO_DELETE = 'toDelete';
	const MAIN_ON_DOMAINS = 'mainOnDomains';
	const NEW_URLS = 'newUrls';

	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		FormFactoryInterface $formFactory,
		FriendlyUrlFacade $friendlyUrlFacade,
		DomainRouterFactory $domainRouterFactory,
		Domain $domain
	) {
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->domain = $domain;
		$this->formFactory = $formFactory;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		if ($options['route_name'] === null) {
			throw new \Shopsys\ShopBundle\Form\Exception\MissingRouteNameException();
		}

		$builder->add(self::TO_DELETE, FormType::FORM);
		$builder->add(self::MAIN_ON_DOMAINS, FormType::FORM);
		$builder->add(self::NEW_URLS, FormType::COLLECTION, [
			'type' => FormType::FRIENDLY_URL,
			'required' => false,
			'allow_add' => true,
			'error_bubbling' => false,
			'constraints' => [
				new UniqueSlugsOnDomains(),
			],
		]);

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
				'data_class' => FriendlyUrl::class,
				'invalid_message' => 'Previously selected main URL dos not exist any more',
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
		$view->vars['domainUrlsById'] = $this->getDomainUrlsIndexedById();
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => UrlListData::class,
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
	 * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[domainId][]
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

	/**
	 * @return string[domainId]
	 */
	private function getDomainUrlsIndexedById() {
		$domainUrlsById = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$domainUrlsById[$domainConfig->getId()] = $domainConfig->getUrl();
		}

		return $domainUrlsById;
	}

}
