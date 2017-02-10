<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Transformers\RemoveWhitespacesTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class MoneyType extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addViewTransformer(new RemoveWhitespacesTransformer());
    }

    /**
     * @return string
     */
    public function getExtendedType() {
        return 'money';
    }
}
