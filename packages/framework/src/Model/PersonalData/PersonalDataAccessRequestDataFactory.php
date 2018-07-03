<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

class PersonalDataAccessRequestDataFactory implements PersonalDataAccessRequestDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData
     */
    public function createForExport(): PersonalDataAccessRequestData
    {
        $personalDataAccessRequestData = new PersonalDataAccessRequestData();
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::TYPE_EXPORT;

        return $personalDataAccessRequestData;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData
     */
    public function createForDisplay(): PersonalDataAccessRequestData
    {
        $personalDataAccessRequestData = new PersonalDataAccessRequestData();
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::TYPE_DISPLAY;

        return $personalDataAccessRequestData;
    }
}
