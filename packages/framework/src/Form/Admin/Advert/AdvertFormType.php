<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Advert;

use Shopsys\FormTypesBundle\YesNoType;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Form\DomainType;
use Shopsys\FrameworkBundle\Form\ImageUploadType;
use Shopsys\FrameworkBundle\Form\ValidationGroup;
use Shopsys\FrameworkBundle\Model\Advert\Advert;
use Shopsys\FrameworkBundle\Model\Advert\AdvertData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class AdvertFormType extends AbstractType
{
    const VALIDATION_GROUP_TYPE_IMAGE = 'typeImage';
    const VALIDATION_GROUP_TYPE_CODE = 'typeCode';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        Domain $domain
    ) {
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imageConstraints = [
            new Constraints\NotBlank([
                'message' => 'Choose image',
                'groups' => [self::VALIDATION_GROUP_TYPE_IMAGE],
            ]),
        ];

        $builderSettingsGroup = $builder->create('settings', FormType::class, [
            'inherit_data' => true,
            'is_group_container' => true,
            'label' => t('Settings'),
        ]);

        if ($options['scenario'] === self::SCENARIO_EDIT) {
            $builderSettingsGroup
                ->add('id', TextType::class, [
                    'required' => false,
                    'data' => $options['advert']->getId(),
                    'mapped' => false,
                    'attr' => ['readonly' => 'readonly'],
                    'label' => t('ID'),
                ])
                ->add('domain', TextType::class, [
                    'required' => false,
                    'data' => $this->domain->getDomainConfigById($options['advert']->getDomainId())->getName(),
                    'mapped' => false,
                    'attr' => ['readonly' => 'readonly'],
                    'label' => t('Domain'),
                ]);
        } else {
            $builderSettingsGroup
                ->add('domainId', DomainType::class, [
                    'required' => true,
                    'constraints' => [
                        new Constraints\NotBlank(),
                    ],
                    'label' => t('Domain'),
                ]);
        }

        $builderSettingsGroup
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name of advertisement area']),
                ],
                'label' => t('Name'),
                'icon_title' => 'Name serves only for internal use within the administration.',
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    t('HTML code') => Advert::TYPE_CODE,
                    t('Image with link') => Advert::TYPE_IMAGE,
                ],
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose advertisement type']),
                ],
                'label' => t('Type'),
            ])
            ->add('positionName', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    t('under heading') => Advert::POSITION_HEADER,
                    t('above footer') => Advert::POSITION_FOOTER,
                    t('in category (above the category name)') => Advert::POSITION_PRODUCT_LIST,
                    t('in left panel (under category tree)') => Advert::POSITION_LEFT_SIDEBAR,
                ],
                'placeholder' => t('-- Choose area --'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose advertisement area']),
                ],
                'label' => t('Area'),
            ])
            ->add('hidden', YesNoType::class, [
                'required' => false,
                'label' => t('Hide advertisement'),
            ])
            ->add('code', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter HTML code for advertisement area',
                        'groups' => [self::VALIDATION_GROUP_TYPE_CODE],
                    ]),
                ],
                'js_container' => [
                    'container_class' => 'js-advert-type-content form-line__js',
                    'data_type' => 'code',
                ],
            ]);

        $builderImageGroup = $builder->create('image_group', FormType::class, [
            'inherit_data' => true,
            'is_group_container' => true,
            'label' => t('Images'),
            'js_container' => [
                'container_class' => 'js-advert-type-content',
                'data_type' => 'image',
            ],
        ]);

        $builderImageGroup
            ->add('link', TextType::class, [
                'required' => false,
                'label' => t('Link'),
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
                'constraints' => ($options['image_exists'] ? [] : $imageConstraints),
                'label' => t('Upload new image'),
                'entity' => $options['advert'],
                'info_text' => t('You can upload following formats: PNG, JPG, GIF'),
            ]);

        $builder
            ->add($builderSettingsGroup)
            ->add($builderImageGroup)
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['scenario', 'advert', 'image_exists'])
            ->setAllowedTypes('image_exists', 'bool')
            ->setAllowedValues('scenario', [self::SCENARIO_CREATE, self::SCENARIO_EDIT])
            ->setAllowedTypes('advert', [Advert::class, 'null'])
            ->setDefaults([
                'image_exists' => false,
                'data_class' => AdvertData::class,
                'attr' => ['novalidate' => 'novalidate'],
                'validation_groups' => function (FormInterface $form) {
                    $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

                    $advertData = $form->getData();
                    /* @var $advertData \Shopsys\FrameworkBundle\Model\Advert\AdvertData */

                    if ($advertData->type === Advert::TYPE_CODE) {
                        $validationGroups[] = self::VALIDATION_GROUP_TYPE_CODE;
                    } elseif ($advertData->type === Advert::TYPE_IMAGE) {
                        $validationGroups[] = self::VALIDATION_GROUP_TYPE_IMAGE;
                    }
                    return $validationGroups;
                },
            ]);
    }
}
