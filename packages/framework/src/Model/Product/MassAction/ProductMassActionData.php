<?php

namespace Shopsys\FrameworkBundle\Model\Product\MassAction;

class ProductMassActionData
{
    public const SELECT_TYPE_CHECKED = 'selectTypeChecked';
    public const SELECT_TYPE_ALL_RESULTS = 'selectTypeAllResults';

    public const ACTION_SET = 'actionSet';

    public const SUBJECT_PRODUCT_HIDDEN = 'subjectProductHidden';

    public const VALUE_PRODUCT_SHOW = 0;
    public const VALUE_PRODUCT_HIDE = 1;

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
