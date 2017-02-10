<?php

namespace Shopsys\ShopBundle\Form\Admin\PromoCode;

use Shopsys\ShopBundle\Component\Constraints\NotInArray;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PromoCodeFormType extends AbstractType {

    /**
     * @var string[]
     */
    private $prohibitedCodes;

    /**
     * @param string[] $prohibitedCodes
     */
    public function __construct(array $prohibitedCodes) {
        $this->prohibitedCodes = $prohibitedCodes;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'promo_code_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('code', FormType::TEXT, [
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
            ->add('percent', FormType::INTEGER, [
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

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => PromoCodeData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }

}
