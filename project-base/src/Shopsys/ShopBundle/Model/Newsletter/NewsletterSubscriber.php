<?php

namespace Shopsys\ShopBundle\Model\Newsletter;

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
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
