<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Model\Localization\IntlCurrencyRepository;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CurrencyType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Localization\IntlCurrencyRepository
     */
    private $intlCurrencyRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(
        IntlCurrencyRepository $intlCurrencyRepository,
        Localization $localization
    ) {
        $this->intlCurrencyRepository = $intlCurrencyRepository;
        $this->localization = $localization;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $intlCurrencies = $this->intlCurrencyRepository->getAll($this->localization->getLocale());

        $choices = [];
        foreach ($intlCurrencies as $intlCurrency) {
            $choices[] = $intlCurrency->getCurrencyCode();
        }

        $resolver->setDefaults([
            'constraints' => [
                new Constraints\Choice([
                    'choices' => $choices,
                    'message' => 'Please enter valid 3-digit currency code according to ISO 4217 standard (uppercase)',
                ]),
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'currency';
    }
}
