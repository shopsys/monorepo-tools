<?php

namespace Shopsys\ShopBundle\Form\Admin\Order;

use Shopsys\ShopBundle\Model\Order\Item\OrderTransportData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderTransportFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    private $transports;

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $transports
     */
    public function __construct(array $transports)
    {
        $this->transports = $transports;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transport', ChoiceType::class, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->transports, 'name', [], null, 'id'),
                'error_bubbling' => true,
            ])
            ->add('priceWithVat', MoneyType::class, [
                'currency' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter unit price with VAT']),
                ],
                'error_bubbling' => true,
            ])
            ->add('vatPercent', MoneyType::class, [
                'currency' => false,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
                'error_bubbling' => true,
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderTransportData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
