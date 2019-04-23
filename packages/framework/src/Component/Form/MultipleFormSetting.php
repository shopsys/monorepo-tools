<?php

namespace Shopsys\FrameworkBundle\Component\Form;

class MultipleFormSetting
{
    public const DEFAULT_MULTIPLE = false;

    /**
     * @var bool
     */
    protected $isCurrentFormMultiple = self::DEFAULT_MULTIPLE;

    public function currentFormIsMultiple()
    {
        $this->isCurrentFormMultiple = true;
    }

    public function currentFormIsNotMultiple()
    {
        $this->isCurrentFormMultiple = false;
    }

    public function reset()
    {
        $this->isCurrentFormMultiple = self::DEFAULT_MULTIPLE;
    }

    /**
     * @return bool
     */
    public function isCurrentFormMultiple()
    {
        return $this->isCurrentFormMultiple;
    }
}
