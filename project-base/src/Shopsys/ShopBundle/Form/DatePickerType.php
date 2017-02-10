<?php

namespace Shopsys\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatePickerType extends AbstractType
{

    const FORMAT_PHP = 'dd.MM.yyyy';
    const FORMAT_JS = 'dd.mm.yy';

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'format' => self::FORMAT_PHP,
        ]);
    }

    public function getParent() {
        return 'date';
    }

    public function getName() {
        return 'date_picker';
    }

}
