<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

class PersonalDataAccessRequestDataFactory
{

    /**
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData
     */
    public function createDefault()
    {
        return new PersonalDataAccessRequestData();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData
     */
    public function createDefaultForExport()
    {
        $personalDataAccessRequestData = $this->createDefault();
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::TYPE_EXPORT;

        return $personalDataAccessRequestData;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData
     */
    public function createDefaultForDisplay()
    {
        $personalDataAccessRequestData = $this->createDefault();
        $personalDataAccessRequestData->type = PersonalDataAccessRequest::TYPE_DISPLAY;

        return $personalDataAccessRequestData;
    }
}
