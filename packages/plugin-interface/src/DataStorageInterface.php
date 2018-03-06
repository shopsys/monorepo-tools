<?php

namespace Shopsys\Plugin;

/**
 * @deprecated
 */
interface DataStorageInterface
{
    /**
     * Returns previously saved value or null if no value is found
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * Returns all previously saved values indexed by keys
     *
     * @return array
     */
    public function getAll();

    /**
     * Returns array of previously saved values indexed by keys
     *
     * Requested items are missing if associated values are not found (values are never null)
     *
     * @param array $keys
     * @return array
     */
    public function getMultiple(array $keys);

    /**
     * Saves a value representable by JSON
     *
     * Saving NULL value is not distinguishable from removing it
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * Removes a previously saved value
     *
     * @param string $key
     */
    public function remove($key);
}
