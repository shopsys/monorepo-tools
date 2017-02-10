<?php

namespace Shopsys\ShopBundle\Component\Grid;

class Column
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $sourceColumnName;

    /**
     * @var string
     */
    private $title;

    /**
     * @var bool
     */
    private $sortable;

    /**
     * @var string
     */
    private $classAttribute;

    /**
     * @var string
     */
    private $orderSourceColmunName;

    /**
     * @param string $id
     * @param string $sourceColumnName
     * @param string $title
     * @param bool $sortable
     */
    public function __construct($id, $sourceColumnName, $title, $sortable) {
        $this->id = $id;
        $this->sourceColumnName = $sourceColumnName;
        $this->title = $title;
        $this->sortable = $sortable;
        $this->classAttribute = '';
        $this->orderSourceColmunName = $sourceColumnName;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSourceColumnName() {
        return $this->sourceColumnName;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function isSortable() {
        return $this->sortable;
    }

    /**
     * @return string
     */
    public function getClassAttribute() {
        return $this->classAttribute;
    }

    /**
     * @param string $class
     * @return \Shopsys\ShopBundle\Component\Grid\Column
     */
    public function setClassAttribute($class) {
        $this->classAttribute = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderSourceColumnName() {
        return $this->orderSourceColmunName;
    }

}
