<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Form\Constraints\UniqueSlugsOnDomains;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlListType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['route_name'] === null) {
            throw new \Shopsys\FrameworkBundle\Form\Exception\MissingRouteNameException();
        }

        $builder->add('toDelete', FormType::class);
        $builder->add('mainFriendlyUrlsByDomainId', FormType::class);
        $builder->add('newUrls', CollectionType::class, [
            'entry_type' => FriendlyUrlType::class,
            'required' => false,
            'allow_add' => true,
            'error_bubbling' => false,
            'constraints' => [
                new UniqueSlugsOnDomains(),
            ],
        ]);

        $friendlyUrlsByDomain = $this->getFriendlyUrlsIndexedByDomain($options['route_name'], $options['entity_id']);

        foreach ($friendlyUrlsByDomain as $domainId => $friendlyUrls) {
            $builder->get('toDelete')->add($domainId, ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $friendlyUrls,
                'choice_label' => 'slug',
                'choice_value' => 'slug',
            ]);
            $builder->get('mainFriendlyUrlsByDomainId')->add($domainId, ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices' => $friendlyUrls,
                'choice_label' => 'slug',
                'choice_value' => 'slug',
                'invalid_message' => 'Previously selected main URL dos not exist any more',
            ]);
        }
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $absoluteUrlsByDomainIdAndSlug = $this->getAbsoluteUrlsIndexedByDomainIdAndSlug(
            $options['route_name'],
            $options['entity_id']
        );
        $mainUrlsSlugsOnDomains = $this->getMainFriendlyUrlSlugsIndexedByDomainId(
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UrlListData::class,
            'required' => false,
            'route_name' => null,
            'entity_id' => null,
        ]);
    }

    /**
     * @param string $routeName
     * @param string $entityId
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl[][]
     */
    private function getFriendlyUrlsIndexedByDomain($routeName, $entityId)
    {
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
     * @return string[][]
     */
    private function getAbsoluteUrlsIndexedByDomainIdAndSlug($routeName, $entityId)
    {
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
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
            }
        }

        return $absoluteUrlsByDomainIdAndSlug;
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @return string[]
     */
    private function getMainFriendlyUrlSlugsIndexedByDomainId($routeName, $entityId)
    {
        $mainFriendlyUrlsSlugsByDomainId = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();
            $mainFriendlyUrl = $this->friendlyUrlFacade->findMainFriendlyUrl(
                $domainId,
                $routeName,
                $entityId
            );
            if ($mainFriendlyUrl !== null) {
                $mainFriendlyUrlsSlugsByDomainId[$domainId] = $mainFriendlyUrl->getSlug();
            } else {
                $mainFriendlyUrlsSlugsByDomainId[$domainId] = null;
            }
        }

        return $mainFriendlyUrlsSlugsByDomainId;
    }

    /**
     * @return string[]
     */
    private function getDomainUrlsIndexedById()
    {
        $domainUrlsById = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainUrlsById[$domainConfig->getId()] = $domainConfig->getUrl();
        }

        return $domainUrlsById;
    }
}
