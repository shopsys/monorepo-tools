<?php

namespace Shopsys\ShopBundle\Component\Validator;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory as BaseJsFormValidatorFactory;
use Symfony\Component\Validator\Constraints;

class JsFormValidatorFactory extends BaseJsFormValidatorFactory
{
    /**
     * @param array $constraints
     * @return array
     */
    protected function parseConstraints(array $constraints) {
        $result = parent::parseConstraints($constraints);

        foreach ($result as $items) {
            foreach ($items as $item) {
                if ($item instanceof Constraints\All) {
                    $item->constraints = $this->parseConstraints($item->constraints);
                }
            }
        }

        return $result;
    }
}
