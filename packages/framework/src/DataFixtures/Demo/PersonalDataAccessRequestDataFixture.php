<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade;

class PersonalDataAccessRequestDataFixture extends AbstractReferenceFixture
{
    const VALID_ACCESS_REQUEST = 'valid_access_request';

    public function load(ObjectManager $manager)
    {
        $personalDataFacade = $this->get(PersonalDataAccessRequestFacade::class);
        /* @var $personalDataFacade \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade */
        $personalDataAccessRequestData = new PersonalDataAccessRequestData();
        $personalDataAccessRequestData->domainId = Domain::FIRST_DOMAIN_ID;
        $personalDataAccessRequestData->email = 'no-reply@netdevelo.cz';
        $personalDataAccessRequestData->hash = 'UrSqiLmCK0cdGfBuwRza';

        $personalDataAccessRequest = $personalDataFacade->createPersonalDataAccessRequest(
            $personalDataAccessRequestData,
            Domain::FIRST_DOMAIN_ID
        );

        $this->addReference(self::VALID_ACCESS_REQUEST, $personalDataAccessRequest);
    }
}
