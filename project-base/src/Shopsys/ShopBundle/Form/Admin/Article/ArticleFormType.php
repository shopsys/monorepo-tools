<?php

namespace Shopsys\ShopBundle\Form\Admin\Article;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\DomainType;
use Shopsys\ShopBundle\Form\UrlListType;
use Shopsys\ShopBundle\Form\YesNoType;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Article\ArticleData;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ArticleFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        SeoSettingFacade $seoSettingFacade,
        Domain $domain
    ) {
        $this->seoSettingFacade = $seoSettingFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $seoMetaDescriptionAttributes = $this->getSeoMetaDescriptionAttributes($options);

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter article name']),
                ],
            ])
            ->add('hidden', YesNoType::class, ['required' => false])
            ->add('text', CKEditorType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter article content']),
                ],
            ])
            ->add('seoTitle', TextType::class, [
                'required' => false,
            ])
            ->add('seoMetaDescription', TextareaType::class, [
                'required' => false,
                'attr' => $seoMetaDescriptionAttributes,
            ])
            ->add('urls', UrlListType::class, [
                'route_name' => 'front_article_detail',
                'entity_id' => $options['article'] !== null ? $options['article']->getId() : null,
            ])
            ->add('save', SubmitType::class);

        if ($options['article'] === null) {
            $builder
                ->add('domainId', DomainType::class, ['required' => true])
                ->add('placement', ChoiceType::class, [
                    'required' => true,
                    'choices' => [
                        t('in upper menu') => Article::PLACEMENT_TOP_MENU,
                        t('in footer') => Article::PLACEMENT_FOOTER,
                        t('without positoning') => Article::PLACEMENT_NONE,
                    ],
                    'placeholder' => t('-- Choose article position --'),
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please choose article placement']),
                    ],
                ]);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['article', 'domain_id'])
            ->setAllowedTypes('article', [Article::class, 'null'])
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
                'data_class' => ArticleData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }

    /**
     * @param array $options
     * @return string[]
     */
    private function getSeoMetaDescriptionAttributes(array $options)
    {
        $seoMetaDescriptionAttributes = [];

        $descriptionsMainPageByDomainIds = $this->seoSettingFacade
            ->getDescriptionsMainPageIndexedByDomainIds($this->domain->getAll());
        $seoMetaDescriptionAttributes['placeholder'] = $this->seoSettingFacade->getDescriptionMainPage($options['domain_id']);

        foreach ($descriptionsMainPageByDomainIds as $domainId => $description) {
            $seoMetaDescriptionAttributes['data-placeholder-domain' . $domainId] = $description;
        }

        return $seoMetaDescriptionAttributes;
    }
}
