<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency\Grid;

use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\FrameworkBundle\Form\Admin\Pricing\Currency\CurrencyFormType;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\Form\FormFactory;

class CurrencyInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        CurrencyGridFactory $currencyGridFactory,
        CurrencyFacade $currencyFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($currencyGridFactory);
        $this->currencyFacade = $currencyFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @return int
     */
    protected function createEntityAndGetId($currencyData)
    {
        $currency = $this->currencyFacade->create($currencyData);

        return $currency->getId();
    }

    /**
     * @param int $currencyId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData $currencyData
     */
    protected function editEntity($currencyId, $currencyData)
    {
        $this->currencyFacade->edit($currencyId, $currencyData);
    }

    /**
     * @param int|null $currencyId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($currencyId)
    {
        $currencyData = new CurrencyData();

        if ($currencyId !== null) {
            $currency = $this->currencyFacade->getById((int)$currencyId);
            $currencyData->setFromEntity($currency);
        }

        return $this->formFactory->create(CurrencyFormType::class, $currencyData, [
            'is_default_currency' => $this->isDefaultCurrencyId($currencyId),
        ]);
    }

    /**
     * @param int|null $currencyId
     * @return bool
     */
    protected function isDefaultCurrencyId($currencyId)
    {
        if ($currencyId !== null) {
            $currency = $this->currencyFacade->getById($currencyId);
            if ($this->currencyFacade->isDefaultCurrency($currency)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return self::class;
    }
}
