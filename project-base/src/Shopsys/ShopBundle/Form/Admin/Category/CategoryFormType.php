<?php

namespace Shopsys\ShopBundle\Form\Admin\Category;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Form\InvertChoiceTypeExtension;
use Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\ShopBundle\Form\DomainsType;
use Shopsys\ShopBundle\Form\FileUploadType;
use Shopsys\ShopBundle\Form\Locale\LocalizedType;
use Shopsys\ShopBundle\Form\UrlListType;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryData;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryRepository;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
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
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryRepository
     */
    private $feedCategoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    private $pluginCrudExtensionFacade;

    public function __construct(
        CategoryFacade $categoryFacade,
        FeedCategoryRepository $feedCategoryRepository,
        Domain $domain,
        SeoSettingFacade $seoSettingFacade,
        PluginCrudExtensionFacade $pluginCrudExtensionFacade
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->feedCategoryRepository = $feedCategoryRepository;
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
        $this->pluginCrudExtensionFacade = $pluginCrudExtensionFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
            $parentChoices = $this->categoryFacade->getAllWithoutBranch($options['category']);
        } else {
            $parentChoices = $this->categoryFacade->getAll();
        }

        $builder
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
            ])
            ->add('seoTitles', MultidomainType::class, [
                'entry_type' => TextType::class,
                'required' => false,
                'options_by_domain_id' => $seoTitlesOptionsByDomainId,
            ])
            ->add('seoMetaDescriptions', MultidomainType::class, [
                'entry_type' => TextareaType::class,
                'required' => false,
                'options_by_domain_id' => $seoMetaDescriptionsOptionsByDomainId,
            ])
            ->add('seoH1s', MultidomainType::class, [
                'required' => false,
                'entry_options' => [
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Heading (H1) cannot be longer than {{ limit }} characters']),
                    ],
                ],
                'options_by_domain_id' => $seoH1OptionsByDomainId,
            ])
            ->add('descriptions', MultidomainType::class, [
                'entry_type' => CKEditorType::class,
                'required' => false,
            ])
            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => $parentChoices,
                'choice_label' => function (Category $category) {
                    $padding = str_repeat("\u{00a0}", ($category->getLevel() - 1) * 2);
                    return $padding . $category->getName();
                },
                'choice_value' => 'id',
            ])
            ->add('showOnDomains', DomainsType::class, [
                InvertChoiceTypeExtension::INVERT_OPTION => true,
                'property_path' => 'hiddenOnDomains',
                'required' => false,
            ])
            ->add('heurekaCzFeedCategory', ChoiceType::class, [
                'required' => false,
                'choices' => $this->feedCategoryRepository->getAllHeurekaCz(),
                'choice_label' => 'name',
                'choice_value' => 'id',
            ])
            ->add('urls', UrlListType::class, [
                'route_name' => 'front_product_list',
                'entity_id' => $options['category'] !== null ? $options['category']->getId() : null,
            ])
            ->add('image', FileUploadType::class, [
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
            ])
            ->add('save', SubmitType::class);

        $this->pluginCrudExtensionFacade->extendForm($builder, 'category', 'pluginData');
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('category')
            ->setAllowedTypes('category', [Category::class, 'null'])
            ->setDefaults([
                'data_class' => CategoryData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\ShopBundle\Model\Category\Category|null $category
     * @return string
     */
    private function getCategoryNameForPlaceholder(DomainConfig $domainConfig, Category $category = null)
    {
        $domainLocale = $domainConfig->getLocale();

        return $category === null ? '' : $category->getName($domainLocale);
    }
}
