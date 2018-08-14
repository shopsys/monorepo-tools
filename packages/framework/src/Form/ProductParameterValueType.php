<?php

namespace Shopsys\FrameworkBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductParameterValueType extends AbstractType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return CollectionType::class;
    }
}
