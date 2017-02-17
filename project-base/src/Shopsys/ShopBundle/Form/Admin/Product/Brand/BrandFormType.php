<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Brand;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Brand\BrandData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class BrandFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('descriptions', FormType::LOCALIZED, [
                'type' => FormType::WYSIWYG,
                'required' => false,
            ])
            ->add('urls', FormType::URL_LIST, [
                'route_name' => 'front_brand_detail',
                'entity_id' => $options['brand'] !== null ? $options['brand']->getId() : null,
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
            ->setRequired('brand')
            ->setAllowedTypes('brand', [Brand::class, 'null'])
            ->setDefaults([
                'data_class' => BrandData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
