<?php

namespace Shopsys\FrameworkBundle\Model\Newsletter;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="newsletter_subscribers",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="newsletter_subscribers_uni",columns={"email", "domain_id"})
 *     }
 * )
 * @ORM\Entity
 */
class NewsletterSubscriber
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", options={"default": "1970-01-01 00:00:00"})
     */
    private $createdAt;

    /**
     * @param string $email
     * @param DateTimeImmutable $createdAt
     * @param int $domainId
     */
    public function __construct(string $email, DateTimeImmutable $createdAt, int $domainId)
    {
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->domainId = $domainId;
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
