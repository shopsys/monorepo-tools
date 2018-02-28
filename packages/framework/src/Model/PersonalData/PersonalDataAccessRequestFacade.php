<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

use DateTime;
use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\String\HashGenerator;

class PersonalDataAccessRequestFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\String\HashGenerator
     */
    private $hashGenerator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestRepository
     */
    private $personalDataAccessRequestRepository;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\FrameworkBundle\Component\String\HashGenerator $hashGenerator
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestRepository $personalDataAccessRequestRepository
     */
    public function __construct(
        EntityManager $em,
        HashGenerator $hashGenerator,
        PersonalDataAccessRequestRepository $personalDataAccessRequestRepository
    ) {
        $this->em = $em;
        $this->hashGenerator = $hashGenerator;
        $this->personalDataAccessRequestRepository = $personalDataAccessRequestRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData $personalDataAccessRequestData
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest
     */
    public function createPersonalDataAccessRequest(
        PersonalDataAccessRequestData $personalDataAccessRequestData,
        $domainId
    ) {
        $hash = $this->getUniqueHash();

        $personalDataAccessRequestData->hash = $hash;
        $personalDataAccessRequestData->createAt = new DateTime();
        $personalDataAccessRequestData->domainId = $domainId;

        $dataAccessRequest = PersonalDataAccessRequest::create($personalDataAccessRequestData);

        $this->em->persist($dataAccessRequest);
        $this->em->flush();

        return $dataAccessRequest;
    }

    /**
     * @param string $hash
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest|null
     */
    public function findEmailByHashAndDomainId($hash, $domainId)
    {
        return $this->personalDataAccessRequestRepository->findByHashAndDomainId($hash, $domainId);
    }

    /**
     * @return string
     */
    private function getUniqueHash()
    {
        do {
            $hash = $this->hashGenerator->generateHash(20);
        } while ($this->personalDataAccessRequestRepository->isHashUsed($hash));

        return $hash;
    }
}
