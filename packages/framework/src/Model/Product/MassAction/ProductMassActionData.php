<?php

namespace Shopsys\FrameworkBundle\Model\Product\MassAction;

class ProductMassActionData
{
    const SELECT_TYPE_CHECKED = 'selectTypeChecked';
    const SELECT_TYPE_ALL_RESULTS = 'selectTypeAllResults';

    const ACTION_SET = 'actionSet';

    const SUBJECT_PRODUCT_HIDDEN = 'subjectProductHidden';

    const VALUE_PRODUCT_SHOW = 0;
    const VALUE_PRODUCT_HIDE = 1;

    /**
     * @var string|null
     */
    public $selectType;

    /**
     * @var string|null
     */
    public $action;

    /**
     * @var string|null
     */
    public $subject;

    /**
     * @var mixed
     */
    public $value;
}
