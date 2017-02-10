<?php

namespace Shopsys\ShopBundle\Model\Product\Flag;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Product\Flag\FlagFormType;
use Shopsys\ShopBundle\Model\Product\Flag\FlagData;
use Shopsys\ShopBundle\Model\Product\Flag\FlagFacade;
use Shopsys\ShopBundle\Model\Product\Flag\FlagGridFactory;
use Symfony\Component\Form\FormFactory;

class FlagInlineEdit extends AbstractGridInlineEdit {

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Flag\FlagFacade
     */
    private $flagFacade;

    /**
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagGridFactory $flagGridFactory
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagFacade $flagFacade
     */
    public function __construct(
        FormFactory $formFactory,
        FlagGridFactory $flagGridFactory,
        FlagFacade $flagFacade
    ) {
        $this->flagFacade = $flagFacade;

        parent::__construct($formFactory, $flagGridFactory);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagData $flagData
     * @return int
     */
    protected function createEntityAndGetId($flagData) {
        $flag = $this->flagFacade->create($flagData);

        return $flag->getId();
    }

    /**
     * @param int $flagId
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagData $flagData
     */
    protected function editEntity($flagId, $flagData) {
        $this->flagFacade->edit($flagId, $flagData);
    }

    /**
     * @param int|null $flagId
     * @return \Shopsys\ShopBundle\Model\Product\Flag\FlagData
     */
    protected function getFormDataObject($flagId = null) {
        $flagData = new FlagData();

        if ($flagId !== null) {
            $flagId = (int)$flagId;
            $flag = $this->flagFacade->getById($flagId);
            $flagData->setFromEntity($flag);
        }

        return $flagData;
    }

    /**
     * @param int $rowId
     * @return \Shopsys\ShopBundle\Form\Admin\Product\Flag\FlagFormType
     */
    protected function getFormType($rowId) {
        return new FlagFormType();
    }

}
