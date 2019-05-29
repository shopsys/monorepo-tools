<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Category;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Form\DisplayOnlyType;
use Shopsys\FrameworkBundle\Form\DomainsType;
use Shopsys\FrameworkBundle\Form\FormRenderingConfigurationExtension;
use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Form\ImageUploadType;
use Shopsys\FrameworkBundle\Form\Locale\LocalizedType;
use Shopsys\FrameworkBundle\Form\UrlListType;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryData;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CategoryFormType extends AbstractType
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    private $pluginCrudExtensionFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade $seoSettingFacade
     * @param \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade $pluginCrudExtensionFacade
     */
    public function __construct(
        CategoryFacade $categoryFacade,
        Domain $domain,
        SeoSettingFacade $seoSettingFacade,
        PluginCrudExtensionFacade $pluginCrudExtensionFacade
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
        $this->pluginCrudExtensionFacade = $pluginCrudExtensionFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $seoTitlesOptionsByDomainId = [];
        $seoMetaDescriptionsOptionsByDomainId = [];
        $seoH1OptionsByDomainId = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();

            $seoTitlesOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getCategoryNameForPlaceholder($domainConfig, $options['category']),
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'category_form_name_' . $domainConfig->getLocale(),
                ],
            ];
            $seoMetaDescriptionsOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->seoSettingFacade->getDescriptionMainPage($domainId),
                ],
            ];
            $seoH1OptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getCategoryNameForPlaceholder($domainConfig, $options['category']),
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'category_form_name_' . $domainConfig->getLocale(),
                ],
            ];
        }

        if ($options['category'] !== null) {
            $parentChoices = $this->categoryFacade->getTranslatedAllWithoutBranch($options['category'], $this->domain->getCurrentDomainConfig());
        } else {
            $parentChoices = $this->categoryFacade->getTranslatedAll($this->domain->getCurrentDomainConfig());
        }

        $builderSettingsGroup = $builder->create('settings', GroupType::class, [
            'label' => t('Settings'),
        ]);

        if ($options['scenario'] === self::SCENARIO_EDIT) {
            $builderSettingsGroup
                ->add('id', DisplayOnlyType::class, [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter article name']),
                    ],
                    'data' => $options['category']->getId(),
                    'label' => t('ID'),
                ]);
        }

        $builderSettingsGroup
            ->add('name', LocalizedType::class, [
                'main_constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                ],
                'entry_options' => [
                    'required' => false,
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters']),
                    ],
                ],
                'label' => t('Name'),
            ])
            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => $parentChoices,
                'choice_label' => function (Category $category) {
                    $padding = str_repeat("\u{00a0}", ($category->getLevel() - 1) * 2);
                    return $padding . $category->getName();
                },
                'choice_value' => 'id',
                'label' => t('Ancestor category'),
            ])
            ->add('enabled', DomainsType::class, [
                'required' => false,
                'label' => t('Display on'),
            ]);

        $builderSeoGroup = $builder->create('seo', GroupType::class, [
            'label' => t('Seo'),
        ]);

        $builderSeoGroup
            ->add('seoTitles', MultidomainType::class, [
                'entry_type' => TextType::class,
                'required' => false,
                'options_by_domain_id' => $seoTitlesOptionsByDomainId,
                'macro' => [
                    'name' => 'seoFormRowMacros.multidomainRow',
                    'recommended_length' => 60,
                ],
                'label' => t('Page title'),
            ])
            ->add('seoMetaDescriptions', MultidomainType::class, [
                'entry_type' => TextareaType::class,
                'required' => false,
                'options_by_domain_id' => $seoMetaDescriptionsOptionsByDomainId,
                'macro' => [
                    'name' => 'seoFormRowMacros.multidomainRow',
                    'recommended_length' => 155,
                ],
                'label' => t('Meta description'),
            ])
            ->add('seoH1s', MultidomainType::class, [
                'required' => false,
                'entry_options' => [
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Heading (H1) cannot be longer than {{ limit }} characters']),
                    ],
                ],
                'options_by_domain_id' => $seoH1OptionsByDomainId,
                'macro' => [
                    'name' => 'seoFormRowMacros.multidomainRow',
                    'recommended_length' => null,
                ],
                'label' => t('Heading (H1)'),
            ]);

        if ($options['scenario'] === self::SCENARIO_EDIT) {
            $builderSeoGroup
                ->add('urls', UrlListType::class, [
                    'route_name' => 'front_product_list',
                    'entity_id' => $options['category'] !== null ? $options['category']->getId() : null,
                    'label' => t('URL addresses'),
                ]);
        }

        $builderDescriptionGroup = $builder->create('description', GroupType::class, [
            'label' => t('Description'),
        ]);

        $builderDescriptionGroup
            ->add('descriptions', MultidomainType::class, [
                'entry_type' => CKEditorType::class,
                'required' => false,
                'display_format' => FormRenderingConfigurationExtension::DISPLAY_FORMAT_MULTIDOMAIN_ROWS_NO_PADDING,
            ]);

        $builderImageGroup = $builder->create('image', GroupType::class, [
            'label' => t('Image'),
        ]);

        $builderImageGroup
            ->add('image', ImageUploadType::class, [
                'required' => false,
                'file_constraints' => [
                    new Constraints\Image([
                        'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
                        'mimeTypesMessage' => 'Image can be only in JPG, GIF or PNG format',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Uploaded image is to large ({{ size }} {{ suffix }}). '
                            . 'Maximum size of an image is {{ limit }} {{ suffix }}.',
                    ]),
                ],
                'label' => t('Upload image'),
                'entity' => $options['category'],
                'info_text' => t('You can upload following formats: PNG, JPG, GIF'),
            ]);

        $builder
            ->add($builderSettingsGroup)
            ->add($builderSeoGroup)
            ->add($builderDescriptionGroup)
            ->add($builderImageGroup)
            ->add('save', SubmitType::class);

        $this->pluginCrudExtensionFacade->extendForm($builder, 'category', 'pluginData');
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['scenario', 'category'])
            ->setAllowedTypes('category', [Category::class, 'null'])
            ->setAllowedValues('scenario', [self::SCENARIO_CREATE, self::SCENARIO_EDIT])
            ->setDefaults([
                'data_class' => CategoryData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Category\Category|null $category
     * @return string
     */
    private function getCategoryNameForPlaceholder(DomainConfig $domainConfig, ?Category $category = null)
    {
        $domainLocale = $domainConfig->getLocale();

        return $category === null ? '' : $category->getName($domainLocale);
    }
}
