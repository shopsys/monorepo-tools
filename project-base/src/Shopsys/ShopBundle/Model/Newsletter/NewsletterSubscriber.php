<?php

namespace Shopsys\ShopBundle\Model\Newsletter;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="newsletter_subscribers")
 * @ORM\Entity
 */
class NewsletterSubscriber
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @ORM\Id
     */
    private $email;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", options={"default": "1970-01-01 00:00:00"})
     */
    private $createdAt;

    /**
     * @param string $email
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(string $email, DateTimeImmutable $createdAt)
    {
        $this->email = $email;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
