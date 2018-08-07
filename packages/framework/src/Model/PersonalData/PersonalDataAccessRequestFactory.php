<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

class PersonalDataAccessRequestFactory implements PersonalDataAccessRequestFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData $data
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest
     */
    public function create(PersonalDataAccessRequestData $data): PersonalDataAccessRequest
    {
        return new PersonalDataAccessRequest($data);
    }
}
