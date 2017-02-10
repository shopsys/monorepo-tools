<?php

namespace Shopsys\ShopBundle\Model\Customer\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Customer\Exception\UserNotFoundException;

class UserNotFoundByEmailAndDomainException extends UserNotFoundException
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $domainId;

    /**
     * @param string $email
     * @param int $domainId
     * @param \Exception|null $previous
     */
    public function __construct($email, $domainId, Exception $previous = null) {
        parent::__construct('User with email "' . $email . '" on domain "' . $domainId . '" not found.', $previous);

        $this->email = $email;
        $this->domainId = $domainId;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getDomainId() {
        return $this->domainId;
    }
}
