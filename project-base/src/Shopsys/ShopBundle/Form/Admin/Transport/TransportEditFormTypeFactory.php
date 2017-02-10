<?php

namespace Shopsys\ShopBundle\Form\Admin\Transport;

use Shopsys\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;

class TransportEditFormTypeFactory {

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory
     */
    private $transportFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    public function __construct(TransportFormTypeFactory $transportFormTypeFactory, CurrencyFacade $currencyFacade) {
        $this->transportFormTypeFactory = $transportFormTypeFactory;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Transport\TransportFormType
     */
    public function create() {
        $currencies = $this->currencyFacade->getAll();

        return new TransportEditFormType($this->transportFormTypeFactory, $currencies);
    }

}
