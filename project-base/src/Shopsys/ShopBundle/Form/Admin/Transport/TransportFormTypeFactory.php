<?php

namespace Shopsys\ShopBundle\Form\Admin\Transport;

use Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository;

class TransportFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository
     */
    private $vatRepository;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository $vatRepository
     */
    public function __construct(VatRepository $vatRepository)
    {
        $this->vatRepository = $vatRepository;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Transport\TransportFormType
     */
    public function create()
    {
        $vats = $this->vatRepository->getAll();

        return new TransportFormType($vats);
    }
}
