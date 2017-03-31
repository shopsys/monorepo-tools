<?php

namespace Shopsys\ShopBundle\Model\Product\Availability;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Product\Availability\AvailabilityFormType;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityGridFactory;
use Symfony\Component\Form\FormFactory;

class AvailabilityInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityGridFactory $availabilityGridFactory
     * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
     * @param \Symfony\Component\Form\FormFactory $formFactory
     */
    public function __construct(
        AvailabilityGridFactory $availabilityGridFactory,
        AvailabilityFacade $availabilityFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($availabilityGridFactory);
        $this->availabilityFacade = $availabilityFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @return int
     */
    protected function createEntityAndGetId($availabilityData)
    {
        $availability = $this->availabilityFacade->create($availabilityData);

        return $availability->getId();
    }

    /**
     * @param int $availabilityId
     * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
     */
    protected function editEntity($availabilityId, $availabilityData)
    {
        $this->availabilityFacade->edit($availabilityId, $availabilityData);
    }

    /**
     * @param int|null $availabilityId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($availabilityId)
    {
        $availabilityData = new AvailabilityData();

        if ($availabilityId !== null) {
            $availability = $this->availabilityFacade->getById((int)$availabilityId);
            $availabilityData->setFromEntity($availability);
        }

        return $this->formFactory->create(AvailabilityFormType::class, $availabilityData);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return 'shopsys.shop.product.availability.availability_inline_edit';
    }
}
