<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade;

class PersonalDataAccessRequestDataFixture extends AbstractReferenceFixture
{
    const REFERENCE_ACCESS_DISPLAY_REQUEST = 'reference_access_display_request';
    const REFERENCE_ACCESS_EXPORT_REQUEST = 'reference_access_export_request';

    /** @var PersonalDataAccessRequestFacade */
    private $personalDataFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade $personalDataFacade
     */
    public function __construct(PersonalDataAccessRequestFacade $personalDataFacade)
    {
        $this->personalDataFacade = $personalDataFacade;
    }

    public function load(ObjectManager $manager)
    {
        $personalDataAccessRequestData = new PersonalDataAccessRequestData();
        $personalDataAccessRequestData->domainId = Domain::FIRST_DOMAIN_ID;
        $personalDataAccessRequestData->email = 'no-reply@shopsys.com';
        $personalDataAccessRequestData->hash = 'UrSqiLmCK0cdGfBuwRza';
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::TYPE_DISPLAY;

        $personalDataAccessRequest = $this->personalDataFacade->createPersonalDataAccessRequest(
            $personalDataAccessRequestData,
            Domain::FIRST_DOMAIN_ID
        );

        $this->addReference(self::REFERENCE_ACCESS_DISPLAY_REQUEST, $personalDataAccessRequest);

        $personalDataAccessRequestData = new PersonalDataAccessRequestData();
        $personalDataAccessRequestData->domainId = Domain::FIRST_DOMAIN_ID;
        $personalDataAccessRequestData->email = 'no-reply@shopsys.com';
        $personalDataAccessRequestData->hash = 'UrSqiLmCK0cdGfBuwRza';
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::TYPE_EXPORT;

        $personalDataAccessRequest = $this->personalDataFacade->createPersonalDataAccessRequest(
            $personalDataAccessRequestData,
            Domain::FIRST_DOMAIN_ID
        );

        $this->addReference(self::REFERENCE_ACCESS_EXPORT_REQUEST, $personalDataAccessRequest);
    }
}
