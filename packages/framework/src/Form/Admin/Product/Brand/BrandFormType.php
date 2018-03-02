<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Product\Brand;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\FrameworkBundle\Form\FileUploadType;
use Shopsys\FrameworkBundle\Form\Locale\LocalizedType;
use Shopsys\FrameworkBundle\Form\UrlListType;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'entity_id' => $options['brand'] !== null ? $options['brand']->getId() : null,
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
            ]);
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
