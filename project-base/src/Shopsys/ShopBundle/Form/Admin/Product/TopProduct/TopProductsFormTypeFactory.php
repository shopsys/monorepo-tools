<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\TopProduct;

use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormType;

class TopProductsFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @param \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
     */
    public function __construct(RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer)
    {
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormType
     */
    public function create()
    {
        return new TopProductsFormType($this->removeDuplicatesTransformer);
    }
}
