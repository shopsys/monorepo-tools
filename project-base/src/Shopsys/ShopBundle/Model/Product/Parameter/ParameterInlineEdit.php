<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\FrameworkBundle\Form\Admin\Product\Parameter\ParameterFormType;
use Symfony\Component\Form\FormFactory;

class ParameterInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterFacade
     */
    private $parameterFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        ParameterGridFactory $parameterGridFactory,
        ParameterFacade $parameterFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($parameterGridFactory);
        $this->parameterFacade = $parameterFacade;
        $this->formFactory = $formFactory;
    }
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return int
     */
    protected function createEntityAndGetId($parameterData)
    {
        $parameter = $this->parameterFacade->create($parameterData);

        return $parameter->getId();
    }

    /**
     * @param int $parameterId
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData $parameterData
     */
    protected function editEntity($parameterId, $parameterData)
    {
        $this->parameterFacade->edit($parameterId, $parameterData);
    }

    /**
     * @param int|null $parameterId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($parameterId)
    {
        $parameterData = new ParameterData();

        if ($parameterId !== null) {
            $parameter = $this->parameterFacade->getById((int)$parameterId);
            $parameterData->setFromEntity($parameter);
        }

        return $this->formFactory->create(ParameterFormType::class, $parameterData);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return self::class;
    }
}
