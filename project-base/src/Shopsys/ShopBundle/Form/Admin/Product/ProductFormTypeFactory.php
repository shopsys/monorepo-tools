<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use Shopsys\ShopBundle\Model\Product\Brand\BrandRepository;
use Shopsys\ShopBundle\Model\Product\Flag\FlagFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\Unit\UnitFacade;

class ProductFormTypeFactory {

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository
     */
    private $vatRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityRepository
     */
    private $availabilityRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandRepository
     */
    private $brandRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Flag\FlagFacade
     */
    private $flagFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade
     */
    private $unitFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        VatRepository $vatRepository,
        AvailabilityRepository $availabilityRepository,
        BrandRepository $brandRepository,
        FlagFacade $flagFacade,
        UnitFacade $unitFacade,
        Domain $domain
    ) {
        $this->vatRepository = $vatRepository;
        $this->availabilityRepository = $availabilityRepository;
        $this->brandRepository = $brandRepository;
        $this->flagFacade = $flagFacade;
        $this->unitFacade = $unitFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product|null $product
     * @return \Shopsys\ShopBundle\Form\Admin\Product\ProductFormType
     */
    public function create(Product $product = null) {
        $vats = $this->vatRepository->getAllIncludingMarkedForDeletion();
        $availabilities = $this->availabilityRepository->getAll();
        $brands = $this->brandRepository->getAll();
        $flags = $this->flagFacade->getAll();
        $units = $this->unitFacade->getAll();

        return new ProductFormType(
            $vats,
            $availabilities,
            $brands,
            $flags,
            $units,
            $this->domain->getAll(),
            $product
        );
    }

}
