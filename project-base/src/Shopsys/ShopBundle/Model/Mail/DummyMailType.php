<?php

namespace Shopsys\ShopBundle\Model\Mail;

use Shopsys\ShopBundle\Model\Mail\MailTypeInterface;

class DummyMailType implements MailTypeInterface
{
    /**
     * @return string[]
     */
    public function getBodyVariables()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getSubjectVariables()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getRequiredBodyVariables()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getRequiredSubjectVariables()
    {
        return [];
    }
}
