<?php

namespace Shopsys\ShopBundle\Form\Admin\Category;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Form\InvertChoiceTypeExtension;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryData;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryRepository;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CategoryFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

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

    public function __construct(
        CategoryRepository $categoryRepository,
        FeedCategoryRepository $feedCategoryRepository,
        Domain $domain,
        SeoSettingFacade $seoSettingFacade
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->feedCategoryRepository = $feedCategoryRepository;
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
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
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();

            $seoTitlesOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getTitlePlaceholder($domainConfig, $options['category']),
                ],
            ];
            $seoMetaDescriptionsOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->seoSettingFacade->getDescriptionMainPage($domainId),
                ],
            ];
        }

        $builder
            ->add('name', FormType::LOCALIZED, [
                'main_constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                ],
                'options' => [
                    'required' => false,
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ])
            ->add('seoTitles', FormType::MULTIDOMAIN, [
                'type' => FormType::TEXT,
                'required' => false,
                'optionsByDomainId' => $seoTitlesOptionsByDomainId,
            ])
            ->add('seoMetaDescriptions', FormType::MULTIDOMAIN, [
                'type' => FormType::TEXTAREA,
                'required' => false,
                'optionsByDomainId' => $seoMetaDescriptionsOptionsByDomainId,
            ])
            ->add('descriptions', FormType::MULTIDOMAIN, [
                'type' => FormType::WYSIWYG,
                'required' => false,
            ])
            ->add('parent', FormType::CHOICE, [
                'required' => false,
                'choice_list' => new ObjectChoiceList($this->categoryRepository->getAll(), 'name', [], null, 'id'),
            ])
            ->add($builder
                ->create('showOnDomains', FormType::DOMAINS, [
                    InvertChoiceTypeExtension::INVERT_OPTION => true,
                    'property_path' => 'hiddenOnDomains',
                    'required' => false,
                ]))
            ->add('heurekaCzFeedCategory', FormType::CHOICE, [
                'required' => false,
                'choice_list' => new ObjectChoiceList($this->feedCategoryRepository->getAllHeurekaCz(), 'name', [], null, 'id'),
            ])
            ->add('urls', FormType::URL_LIST, [
                'route_name' => 'front_product_list',
                'entity_id' => $options['category'] !== null ? $options['category']->getId() : null,
            ])
            ->add('image', FormType::FILE_UPLOAD, [
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
            ->add('save', FormType::SUBMIT);
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
    private function getTitlePlaceholder(DomainConfig $domainConfig, Category $category = null)
    {
        $domainLocale = $domainConfig->getLocale();

        return $category === null ? '' : $category->getName($domainLocale);
    }
}
