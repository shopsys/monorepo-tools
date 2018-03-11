<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Slider;

use Shopsys\FormTypesBundle\YesNoType;
use Shopsys\FrameworkBundle\Form\DomainType;
use Shopsys\FrameworkBundle\Form\FileUploadType;
use Shopsys\FrameworkBundle\Model\Slider\SliderItem;
use Shopsys\FrameworkBundle\Model\Slider\SliderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class SliderItemFormType extends AbstractType
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imageConstraints = [];
        if ($options['scenario'] === self::SCENARIO_CREATE) {
            $imageConstraints[] = new Constraints\NotBlank(['message' => 'Please choose image']);
        }

        $builderSettingsGroup = $builder->create('settings', FormType::class, [
            'inherit_data' => true,
            'is_group_container' => true,
            'label' => t('Settings'),
        ]);

        if ($options['scenario'] === self::SCENARIO_EDIT) {
            $builderSettingsGroup
                ->add('id', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter article name']),
                    ],
                    'data' => $options['slider_item']->getId(),
                    'mapped' => false,
                    'attr' => ['readonly' => 'readonly'],
                    'label' => t('ID'),
                ])
                ->add('domainId', DomainType::class, [
                    'required' => true,
                    'attr' => ['readonly' => 'readonly'],
                    'label' => t('Domain'),
                ]);
        }

        if ($options['scenario'] === self::SCENARIO_CREATE) {
            $builderSettingsGroup->add('domainId', DomainType::class, [
                'required' => true,
                'label' => t('Domain'),
            ]);
        }

        $builderSettingsGroup
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                ],
                'label' => t('Name'),
                'icon_title' => t('Name serves only for internal use within the administration'),

            ])
            ->add('link', UrlType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter link']),
                    new Constraints\Url(['message' => 'Link must be valid URL address']),
                ],
                'label' => t('Link'),
            ])
            ->add('hidden', YesNoType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\NotNull([
                        'message' => 'Please choose visibility',
                    ]),
                ],
                'label' => t('Hide'),
            ]);

        $builderImageGroup = $builder->create('image', FormType::class, [
            'inherit_data' => true,
            'is_group_container' => true,
            'is_group_container_to_render_as_the_last_one' => true,
            'label' => t('Image'),
        ]);

        if ($options['scenario'] === self::SCENARIO_EDIT) {
            $builderImageGroup
                ->add('image_preview', FormType::class, [
                    'data' => $options['slider_item'],
                    'mapped' => false,
                    'required' => false,
                    'label' => t('Image'),
                    'image_preview' => [
                        'size' => 'original',
                        'height' => 100,
                    ],
                ]);
        }

        $builderImageGroup
            ->add('image', FileUploadType::class, [
                'required' => $options['scenario'] === self::SCENARIO_CREATE,
                'constraints' => $imageConstraints,
                'file_constraints' => [
                    new Constraints\Image([
                        'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg'],
                        'mimeTypesMessage' => 'Image can be only in JPG or PNG format',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Uploaded image is to large ({{ size }} {{ suffix }}). '
                            . 'Maximum size of an image is {{ limit }} {{ suffix }}.',
                    ]),
                ],
                'label' => t('Upload image'),
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
            ->setRequired(['scenario', 'slider_item'])
            ->addAllowedTypes('slider_item', [SliderItem::class, 'null'])
            ->addAllowedValues('scenario', [self::SCENARIO_CREATE, self::SCENARIO_EDIT])
            ->setDefaults([
                'data_class' => SliderItemData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
