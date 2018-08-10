<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestDataFactory;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade;

class PersonalDataAccessRequestDataFixture extends AbstractReferenceFixture
{
    const REFERENCE_ACCESS_DISPLAY_REQUEST = 'reference_access_display_request';
    const REFERENCE_ACCESS_EXPORT_REQUEST = 'reference_access_export_request';

    /** @var \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade */
    private $personalDataFacade;

    /** @var \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestDataFactory */
    private $personalDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade $personalDataFacade
     */
    public function __construct(PersonalDataAccessRequestFacade $personalDataFacade, PersonalDataAccessRequestDataFactory $personalDataFactory)
    {
        $this->personalDataFacade = $personalDataFacade;
        $this->personalDataFactory = $personalDataFactory;
    }

    public function load(ObjectManager $manager)
    {
        $personalDataAccessRequestData = $this->personalDataFactory->createForDisplay();
        $personalDataAccessRequestData->domainId = Domain::FIRST_DOMAIN_ID;
        $personalDataAccessRequestData->email = 'no-reply@shopsys.com';
        $personalDataAccessRequestData->hash = 'UrSqiLmCK0cdGfBuwRza';

        $personalDataAccessRequest = $this->personalDataFacade->createPersonalDataAccessRequest(
            $personalDataAccessRequestData,
            Domain::FIRST_DOMAIN_ID
        );

        $this->addReference(self::REFERENCE_ACCESS_DISPLAY_REQUEST, $personalDataAccessRequest);

        $personalDataAccessRequestData = $this->personalDataFactory->createForExport();
        $personalDataAccessRequestData->domainId = Domain::FIRST_DOMAIN_ID;
        $personalDataAccessRequestData->email = 'no-reply@shopsys.com';
        $personalDataAccessRequestData->hash = 'UrSqiLmCK0cdGfBuwRza';

        $personalDataAccessRequest = $this->personalDataFacade->createPersonalDataAccessRequest(
            $personalDataAccessRequestData,
            Domain::FIRST_DOMAIN_ID
        );

        $this->addReference(self::REFERENCE_ACCESS_EXPORT_REQUEST, $personalDataAccessRequest);
    }
}
