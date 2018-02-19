<?php

namespace Shopsys\ShopBundle\Component\Grid\InlineEdit;

use Shopsys\ShopBundle\Component\Grid\GridFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractGridInlineEdit implements GridInlineEditInterface
{
    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactoryInterface
     */
    private $gridFactory;

    public function __construct(GridFactoryInterface $gridFactory)
    {
        $this->gridFactory = $gridFactory;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int|string|null $rowId
     * @return int|string
     */
    public function saveForm(Request $request, $rowId)
    {
        $form = $this->getForm($rowId);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $formErrors = [];
            foreach ($form->getErrors(true) as $error) {
                /* @var $error \Symfony\Component\Form\FormError */
                $formErrors[] = $error->getMessage();
            }
            throw new \Shopsys\ShopBundle\Component\Grid\InlineEdit\Exception\InvalidFormDataException($formErrors);
        }

        $formData = $form->getData();
        if ($rowId !== null) {
            $this->editEntity($rowId, $formData);
        } else {
            $rowId = $this->createEntityAndGetId($formData);
        }

        return $rowId;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Grid\Grid
     */
    public function getGrid()
    {
        $grid = $this->gridFactory->create();
        $grid->setInlineEditService($this);

        return $grid;
    }

    /**
     * @return bool
     */
    public function canAddNewRow()
    {
        return true;
    }

    /**
     * @return string
     */
    abstract public function getServiceName();

    /**
     * @param int|string|null $rowId
     * @return \Symfony\Component\Form\FormInterface
     */
    abstract public function getForm($rowId);

    /**
     * @param int|string $rowId
     * @param mixed $formData
     */
    abstract protected function editEntity($rowId, $formData);

    /**
     * @param mixed $formData
     * @return int|string
     */
    abstract protected function createEntityAndGetId($formData);
}
