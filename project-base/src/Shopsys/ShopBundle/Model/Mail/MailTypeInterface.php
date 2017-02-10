<?php

namespace Shopsys\ShopBundle\Model\Mail;

interface MailTypeInterface
{

    /**
     * @return string[]
     */
    public function getSubjectVariables();

    /**
     * @return string[]
     */
    public function getBodyVariables();

    /**
     * @return string[]
     */
    public function getRequiredSubjectVariables();

    /**
     * @return string[]
     */
    public function getRequiredBodyVariables();
}
