<?php

namespace Shopsys\ShopBundle\Model\Product\Parameter;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Product\Parameter\ParameterFormType;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterData;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterGridFactory;
use Symfony\Component\Form\FormFactory;

class ParameterInlineEdit extends AbstractGridInlineEdit
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade
     */
    private $parameterFacade;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterGridFactory $parameterGridFactory
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade $parameterFacade
     */
    public function __construct(
        FormFactory $formFactory,
        ParameterGridFactory $parameterGridFactory,
        ParameterFacade $parameterFacade
    ) {
        $this->parameterFacade = $parameterFacade;

        parent::__construct($formFactory, $parameterGridFactory);
    }
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return int
     */
    protected function createEntityAndGetId($parameterData) {
        $parameter = $this->parameterFacade->create($parameterData);

        return $parameter->getId();
    }

    /**
     * @param int $parameterId
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
     */
    protected function editEntity($parameterId, $parameterData) {
        $this->parameterFacade->edit($parameterId, $parameterData);
    }

    /**
     * @param int|null $parameterId
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ParameterData
     */
    protected function getFormDataObject($parameterId = null) {
        $parameterData = new ParameterData();

        if ($parameterId !== null) {
            $parameterId = (int)$parameterId;
            $parameter = $this->parameterFacade->getById($parameterId);
            $parameterData->setFromEntity($parameter);
        }

        return $parameterData;
    }

    /**
     * @param int $rowId
     * @return \Shopsys\ShopBundle\Form\Admin\Product\Parameter\ParameterFormType
     */
    protected function getFormType($rowId) {
        return new ParameterFormType();
    }

}
