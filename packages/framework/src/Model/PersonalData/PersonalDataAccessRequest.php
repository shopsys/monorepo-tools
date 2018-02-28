<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="personal_data_access_request")
 */
class PersonalDataAccessRequest
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $hash;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData $personalDataAccessRequestData
     */
    public function __construct(PersonalDataAccessRequestData $personalDataAccessRequestData)
    {
        $this->email = $personalDataAccessRequestData->email;
        $this->createdAt = $personalDataAccessRequestData->createAt;
        $this->hash = $personalDataAccessRequestData->hash;
        $this->domainId = $personalDataAccessRequestData->domainId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestData $personalDataAccessRequestData
     * @return \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest
     */
    public static function create(PersonalDataAccessRequestData $personalDataAccessRequestData)
    {
        return new self($personalDataAccessRequestData);
    }
}
