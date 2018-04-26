<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Transport;

use Shopsys\FrameworkBundle\Form\PriceTableType;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Transport\Detail\TransportDetail;
use Shopsys\FrameworkBundle\Model\Transport\TransportEditData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportEditFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    public function __construct(CurrencyFacade $currencyFacade)
    {
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transportDetail = $options['transport_detail'];
        /* @var $transportDetail \Shopsys\FrameworkBundle\Model\Transport\Detail\TransportDetail */

        $builder
            ->add('transportData', TransportFormType::class, [
                'transport' => $transportDetail !== null ? $transportDetail->getTransport() : null,
            ])
            ->add('pricesByCurrencyId', PriceTableType::class, [
                'currencies' => $this->currencyFacade->getAllIndexedById(),
                'base_prices' => $transportDetail !== null ? $transportDetail->getBasePricesByCurrencyId() : [],
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('transport_detail')
            ->setAllowedTypes('transport_detail', [TransportDetail::class, 'null'])
            ->setDefaults([
                'data_class' => TransportEditData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
