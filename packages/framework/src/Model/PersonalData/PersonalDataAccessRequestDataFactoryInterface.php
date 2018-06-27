<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

interface PersonalDataAccessRequestDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData
     */
    public function createForExport(): PersonalDataAccessRequestData;

    /**
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData
     */
    public function createForDisplay(): PersonalDataAccessRequestData;
}
