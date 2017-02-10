<?php

namespace Shopsys\ShopBundle\Model\ContactForm;

class ContactFormData {

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $email;

    /**
     * @param string $name
     * @param string $message
     * @param string $email
     */
    public function __construct($name = null, $message = null, $email = null) {
        $this->name = $name;
        $this->message = $message;
        $this->email = $email;
    }
}
