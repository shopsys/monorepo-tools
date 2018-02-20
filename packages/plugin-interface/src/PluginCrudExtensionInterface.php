<?php

namespace Shopsys\Plugin;

interface PluginCrudExtensionInterface
{
    /**
     * Returns a class name of a form type to be used
     *
     * Should return FQCN of a implementation of \Symfony\Component\Form\FormTypeInterface to be used as a sub-form
     *
     * @return string
     */
    public function getFormTypeClass();

    /**
     * Returns a human readable label of the sub-form
     *
     * @return string
     */
    public function getFormLabel();

    /**
     * Returns the data of an entity with provided id to be fed into the sub-form
     *
     * @param int $id
     * @return mixed
     */
    public function getData($id);

    /**
     * Saves the data of an entity with provided id after submitting of the sub-form
     *
     * @param int $id
     * @param mixed $data
     */
    public function saveData($id, $data);

    /**
     * Removes all saved data of an entity with provided id after deleting the entity
     *
     * @param int $id
     */
    public function removeData($id);
}
