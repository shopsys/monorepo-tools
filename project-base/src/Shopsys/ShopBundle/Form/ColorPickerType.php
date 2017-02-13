<?php

namespace Shopsys\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;

class ColorPickerType extends AbstractType
{
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'color_picker';
    }
}
