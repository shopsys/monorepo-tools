<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Article;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\FormTypesBundle\YesNoType;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Form\DomainType;
use Shopsys\FrameworkBundle\Form\UrlListType;
use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleData;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ArticleFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $seoMetaDescriptionAttributes = $this->getSeoMetaDescriptionAttributes($options);

        $builderArticleData = $builder->create('articleData', FormType::class, [
            'inherit_data' => true,
            'label' => t('Article data'),
            'is_group_container' => true,
        ]);

        if ($options['article'] === null) {
            $builderArticleData
                ->add('domainId', DomainType::class, [
                    'required' => true,
                    'data' => $options['domain_id'],
                    'label' => t('Domain'),
                ])
                ->add('placement', ChoiceType::class, [
                    'required' => true,
                    'choices' => [
                        t('in upper menu') => Article::PLACEMENT_TOP_MENU,
                        t('in footer') => Article::PLACEMENT_FOOTER,
                        t('without positioning') => Article::PLACEMENT_NONE,
                    ],
                    'placeholder' => t('-- Choose article position --'),
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please choose article placement']),
                    ],
                    'label' => t('Location'),
                ]);
        } else {
            $builderArticleData
                ->add('id', TextType::class, [
                    'required' => false,
                    'data' => $options['article']->getId(),
                    'mapped' => false,
                    'attr' => ['readonly' => 'readonly'],
                    'label' => t('ID'),
                ])
                ->add('domain', TextType::class, [
                    'required' => false,
                    'data' => $this->domain->getDomainConfigById($options['article']->getDomainId())->getName(),
                    'mapped' => false,
                    'attr' => ['readonly' => 'readonly'],
                    'label' => t('Domain'),
                ]);
        }
        $builderArticleData
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter article name']),
                ],
                'label' => t('Name'),
            ])
            ->add('hidden', YesNoType::class, [
                'required' => false,
                'label' => t('Hide'),
            ])
            ->add('text', CKEditorType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter article content']),
                ],
                'label' => t('Content'),
            ]);

        $builderSeoData = $builder->create('seo', FormType::class, [
            'inherit_data' => true,
            'label' => t('SEO'),
            'is_group_container' => true,
            'is_group_container_to_render_as_the_last_one' => true,
        ]);

        $builderSeoData
            ->add('seoTitle', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'article_form_name',
                ],
                'label' => t('Page title'),
                'macro' => [
                    'name' => 'seoFormRowMacros',
                    'recommended_length' => 60,
                ],
            ])
            ->add('seoMetaDescription', TextareaType::class, [
                'required' => false,
                'attr' => $seoMetaDescriptionAttributes,
                'label' => t('Meta description'),
                'macro' => [
                    'name' => 'seoFormRowMacros',
                    'recommended_length' => 155,
                ],
            ])
            ->add('seoH1', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'article_form_name',
                ],
                'label' => t('Heading (H1)'),
            ]);

        if ($options['article'] !== null) {
            $builderSeoData
                ->add('urls', UrlListType::class, [
                    'label' => t('URL addresses'),
                    'route_name' => 'front_article_detail',
                    'entity_id' => $options['article'] !== null ? $options['article']->getId() : null,
                ]);
        }

        $builder
            ->add($builderArticleData)
            ->add($builderSeoData)
            ->add('save', SubmitType::class);
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
