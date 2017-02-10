<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Parameter;

use Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository;

class ProductParameterValueFormTypeFactory
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository
     */
    private $parameterRepository;

    public function __construct(
        ParameterRepository $parameterRepository
    ) {
        $this->parameterRepository = $parameterRepository;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormType
     */
    public function create() {
        $parameters = $this->parameterRepository->getAll();

        return new ProductParameterValueFormType($parameters);
    }
}
