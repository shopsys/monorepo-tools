<?php

namespace Shopsys\ShopBundle\Form\Admin\Advert;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Advert\Advert;
use Shopsys\ShopBundle\Model\Advert\AdvertData;
use Shopsys\ShopBundle\Model\Advert\AdvertPositionList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class AdvertFormType extends AbstractType
{
    const VALIDATION_GROUP_TYPE_IMAGE = 'typeImage';
    const VALIDATION_GROUP_TYPE_CODE = 'typeCode';

    /**
     * @var \Shopsys\ShopBundle\Model\Advert\AdvertPositionList
     */
    private $advertPositionList;

    public function __construct(AdvertPositionList $advertPositionList)
    {
        $this->advertPositionList = $advertPositionList;
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
        $builder
            ->add('domainId', FormType::DOMAIN, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add('name', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name of advertisment area']),
                ],
            ])
            ->add('type', FormType::CHOICE, [
                'required' => true,
                'choices' => $this->getTypeChoices(),
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose advertisement type']),
                ],
            ])
            ->add('positionName', FormType::CHOICE, [
                'required' => true,
                'choices' => $this->advertPositionList->getTranslationsIndexedByValue(),
                'placeholder' => t('-- Choose area --'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose advertisement area']),
                ],
            ])
            ->add('code', FormType::TEXTAREA, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter HTML code for advertisement area',
                        'groups' => [self::VALIDATION_GROUP_TYPE_CODE],
                    ]),
                ],
            ])
            ->add('hidden', FormType::YES_NO, ['required' => false])
            ->add('link', FormType::TEXT, ['required' => false])
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
                'constraints' => ($options['image_exists'] ? [] : $imageConstraints),
            ])
            ->add('save', FormType::SUBMIT);
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

    /**
     * @return string[]
     */
    private function getTypeChoices()
    {
        return [
            Advert::TYPE_CODE => t('HTML code'),
            Advert::TYPE_IMAGE => t('Picture with link'),
        ];
    }
}
