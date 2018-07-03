<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\FrameworkBundle\Form\Admin\Product\Availability\AvailabilityFormType;
use Symfony\Component\Form\FormFactoryInterface;

class AvailabilityInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityDataFactoryInterface
     */
    private $availabilityDataFactory;

    public function __construct(
        AvailabilityGridFactory $availabilityGridFactory,
        AvailabilityFacade $availabilityFacade,
        FormFactoryInterface $formFactory,
        AvailabilityDataFactoryInterface $availabilityDataFactory
    ) {
        parent::__construct($availabilityGridFactory);
        $this->availabilityFacade = $availabilityFacade;
        $this->formFactory = $formFactory;
        $this->availabilityDataFactory = $availabilityDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     * @return int
     */
    protected function createEntityAndGetId($availabilityData)
    {
        $availability = $this->availabilityFacade->create($availabilityData);

        return $availability->getId();
    }

    /**
     * @param int $availabilityId
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
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
        if ($availabilityId !== null) {
            $availability = $this->availabilityFacade->getById((int)$availabilityId);
            $availabilityData = $this->availabilityDataFactory->createFromAvailability($availability);
        } else {
            $availabilityData = $this->availabilityDataFactory->create();
        }

        return $this->formFactory->create(AvailabilityFormType::class, $availabilityData);
    }
}
