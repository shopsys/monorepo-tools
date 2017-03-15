<?php

namespace Shopsys\ShopBundle\Form\Admin\PromoCode;

use Shopsys\ShopBundle\Component\Constraints\NotInArray;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class PromoCodeFormType extends AbstractType
{
    /**
     * @var string[]
     */
    private $prohibitedCodes;

    /**
     * @param string[] $prohibitedCodes
     */
    public function __construct(array $prohibitedCodes)
    {
        $this->prohibitedCodes = $prohibitedCodes;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter code',
                    ]),
                    new NotInArray([
                        'array' => $this->prohibitedCodes,
                        'message' => 'Discount coupon with this code already exists',
                    ]),
                ],
            ])
            ->add('percent', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter discount percentage',
                    ]),
                    new Constraints\Range([
                        'min' => 0,
                        'max' => 100,
                    ]),
                ],
                'invalid_message' => 'Please enter whole number.',
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PromoCodeData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
