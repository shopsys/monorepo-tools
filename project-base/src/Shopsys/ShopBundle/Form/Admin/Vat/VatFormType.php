<?php

namespace Shopsys\ShopBundle\Form\Admin\Vat;

use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class VatFormType extends AbstractType
{
    const SCENARIO_CREATE = 1;
    const SCENARIO_EDIT = 2;

    /**
     * @var bool
     */
    private $scenario;

    /**
     * @param int $scenario
     */
    public function __construct($scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT name']),
                    new Constraints\Length(['max' => 50, 'maxMessage' => 'VAT name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('percent', NumberType::class, [
                'required' => false,
                'precision' => 4,
                'disabled' => $this->scenario === self::SCENARIO_EDIT,
                'read_only' => $this->scenario === self::SCENARIO_EDIT,
                'invalid_message' => 'Please enter VAT in correct format.',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VatData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
