<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Product\Brand;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Form\ImageUploadType;
use Shopsys\FrameworkBundle\Form\Locale\LocalizedType;
use Shopsys\FrameworkBundle\Form\UrlListType;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class BrandFormType extends AbstractType
{

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade $seoSettingFacade
     */
    public function __construct(
        Domain $domain,
        SeoSettingFacade $seoSettingFacade
    ) {
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $brand = $options['brand'];
        /* @var $brand \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|null */

        $seoTitlesOptionsByDomainId = [];
        $seoMetaDescriptionsOptionsByDomainId = [];
        $seoH1sOptionsByDomainId = [];

        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();

            $seoTitlesOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getTitlePlaceholder($brand),
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'brand_form_name',
                ],
            ];
            $seoMetaDescriptionsOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->seoSettingFacade->getDescriptionMainPage($domainId),
                ],
            ];
            $seoH1sOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getTitlePlaceholder($brand),
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'brand_form_name',
                ],
            ];
        }

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('descriptions', LocalizedType::class, [
                'entry_type' => CKEditorType::class,
                'required' => false,
            ])
            ->add('urls', UrlListType::class, [
                'route_name' => 'front_brand_detail',
                'entity_id' => $brand !== null ? $brand->getId() : null,
            ])
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
                'entity' => $brand,
                'info_text' => t('You can upload following formats: PNG, JPG, GIF'),
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
                'entry_type' => TextType::class,
                'required' => false,
                'options_by_domain_id' => $seoH1sOptionsByDomainId,
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('brand')
            ->setAllowedTypes('brand', [Brand::class, 'null'])
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|null $brand
     * @return string
     */
    private function getTitlePlaceholder(Brand $brand = null)
    {
        return $brand !== null ? $brand->getName() : '';
    }
}
