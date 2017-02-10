<?php

namespace Shopsys\ShopBundle\Form\Admin\Mail;

use Shopsys\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormType;
use Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail;

class AllMailTemplatesFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail
     */
    private $resetPasswordMail;

    public function __construct(ResetPasswordMail $resetPasswordMail) {
        $this->resetPasswordMail = $resetPasswordMail;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormType
     */
    public function create() {
        return new AllMailTemplatesFormType(
            $this->resetPasswordMail
        );
    }
}
