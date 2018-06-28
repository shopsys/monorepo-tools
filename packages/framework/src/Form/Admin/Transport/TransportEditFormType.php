<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Transport;

use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Form\PriceTableType;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportData;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
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

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade
     */
    private $transportFacade;

    public function __construct(CurrencyFacade $currencyFacade, TransportFacade $transportFacade)
    {
        $this->currencyFacade = $currencyFacade;
        $this->transportFacade = $transportFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transport = $options['transport'];
        /* @var $transport \Shopsys\FrameworkBundle\Model\Transport\Transport */

        $builderPricesGroup = $builder->create('prices', GroupType::class, [
            'label' => t('Prices'),
            'is_group_container_to_render_as_the_last_one' => true,
        ]);
        $builderPricesGroup
            ->add('pricesByCurrencyId', PriceTableType::class, [
                'currencies' => $this->currencyFacade->getAllIndexedById(),
                'base_prices' => $transport !== null ? $this->transportFacade->getIndependentBasePricesIndexedByCurrencyId($transport) : [],
            ]);

        $builder
            ->add('transportData', TransportFormType::class, [
                'transport' => $transport,
                'render_form_row' => false,
                'inherit_data' => true,
            ])
            ->add($builderPricesGroup)
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('transport')
            ->setAllowedTypes('transport', [Transport::class, 'null'])
            ->setDefaults([
                'data_class' => TransportData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
