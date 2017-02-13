<?php

namespace Shopsys\ShopBundle\Form\Admin\Payment;

use Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository;
use Shopsys\ShopBundle\Model\Transport\TransportRepository;

class PaymentFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportRepository
     */
    private $transportRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository
     */
    private $vatRepository;

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\TransportRepository $transportRepository
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository $vatRepository
     */
    public function __construct(
        TransportRepository $transportRepository,
        VatRepository $vatRepository
    ) {
        $this->transportRepository = $transportRepository;
        $this->vatRepository = $vatRepository;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Payment\PaymentFormType
     */
    public function create()
    {
        $allTransports = $this->transportRepository->getAll();
        $vats = $this->vatRepository->getAll();

        return new PaymentFormType($allTransports, $vats);
    }
}
