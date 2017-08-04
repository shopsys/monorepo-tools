<?php

namespace Shopsys\ShopBundle\Form\Admin\Advert;

use Shopsys\FormTypesBundle\YesNoType;
use Shopsys\ShopBundle\Form\DomainType;
use Shopsys\ShopBundle\Form\FileUploadType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Advert\Advert;
use Shopsys\ShopBundle\Model\Advert\AdvertData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $builder
            ->add('domainId', DomainType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name of advertisment area']),
                ],
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
            ])
            ->add('code', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter HTML code for advertisement area',
                        'groups' => [self::VALIDATION_GROUP_TYPE_CODE],
                    ]),
                ],
            ])
            ->add('hidden', YesNoType::class, ['required' => false])
            ->add('link', TextType::class, ['required' => false])
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
                'constraints' => ($options['image_exists'] ? [] : $imageConstraints),
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('image_exists')
            ->setAllowedTypes('image_exists', 'bool')
            ->setDefaults([
                'image_exists' => false,
                'data_class' => AdvertData::class,
                'attr' => ['novalidate' => 'novalidate'],
                'validation_groups' => function (FormInterface $form) {
                    $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

                    $advertData = $form->getData();
                    /* @var $advertData \Shopsys\ShopBundle\Model\Advert\AdvertData */

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
