<?php

namespace Shopsys\ShopBundle\Model\Customer\Exception;

use Exception;

class DuplicateEmailException extends Exception implements CustomerException
{
    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email
     * @param \Exception|null $previous
     */
    public function __construct($email, $previous = null) {
        $this->email = $email;

        parent::__construct('User with email ' . $this->email . ' already exists.', 0, $previous);
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
}
