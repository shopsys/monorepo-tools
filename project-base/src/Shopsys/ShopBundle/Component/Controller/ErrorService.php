<?php

namespace Shopsys\ShopBundle\Component\Controller;

use Shopsys\ShopBundle\Component\FlashMessage\Bag;
use Symfony\Component\Form\Form;

class ErrorService {

    /**
     * @param \Symfony\Component\Form\Form $form
     * @param \Shopsys\ShopBundle\Component\FlashMessage\Bag $flashMessageBag
     * @return string[]
     */
    public function getAllErrorsAsArray(Form $form, Bag $flashMessageBag) {
        $errors = $flashMessageBag->getErrorMessages();
        foreach ($form->getErrors(true) as $error) {
            /* @var $error \Symfony\Component\Form\FormError */
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}
