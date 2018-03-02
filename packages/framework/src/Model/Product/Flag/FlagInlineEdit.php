<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\FrameworkBundle\Form\Admin\Product\Flag\FlagFormType;
use Symfony\Component\Form\FormFactory;

class FlagInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagFacade
     */
    private $flagFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        FlagGridFactory $flagGridFactory,
        FlagFacade $flagFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($flagGridFactory);
        $this->flagFacade = $flagFacade;
        $this->formFactory = $formFactory;
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
        $flagData = new FlagData();

        if ($flagId !== null) {
            $flag = $this->flagFacade->getById((int)$flagId);
            $flagData->setFromEntity($flag);
        }

        return $this->formFactory->create(FlagFormType::class, $flagData);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return 'shopsys.shop.product.flag.flag_inline_edit';
    }
}
