<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\FrameworkBundle\Form\Admin\Product\Flag\FlagFormType;
use Symfony\Component\Form\FormFactoryInterface;

class FlagInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade
     */
    private $flagFacade;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagDataFactoryInterface
     */
    private $flagDataFactory;

    public function __construct(
        FlagGridFactory $flagGridFactory,
        FlagFacade $flagFacade,
        FormFactoryInterface $formFactory,
        FlagDataFactoryInterface $flagDataFactory
    ) {
        parent::__construct($flagGridFactory);
        $this->flagFacade = $flagFacade;
        $this->formFactory = $formFactory;
        $this->flagDataFactory = $flagDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $flagData
     * @return int
     */
    protected function createEntityAndGetId($flagData)
    {
        $flag = $this->flagFacade->create($flagData);

        return $flag->getId();
    }

    /**
     * @param int $flagId
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $flagData
     */
    protected function editEntity($flagId, $flagData)
    {
        $this->flagFacade->edit($flagId, $flagData);
    }

    /**
     * @param int|null $flagId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($flagId)
    {
        if ($flagId !== null) {
            $flag = $this->flagFacade->getById((int)$flagId);
            $flagData = $this->flagDataFactory->createFromFlag($flag);
        } else {
            $flagData = $this->flagDataFactory->create();
        }

        return $this->formFactory->create(FlagFormType::class, $flagData);
    }
}
