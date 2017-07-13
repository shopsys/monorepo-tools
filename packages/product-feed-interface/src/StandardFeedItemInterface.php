<?php

namespace Shopsys\ProductFeed;

interface StandardFeedItemInterface extends FeedItemInterface
{
    /**
     * @return string
     */
    public function getProductName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string|null
     */
    public function getImgUrl();

    /**
     * @return string
     */
    public function getPriceVat();

    /**
     * @return string|null
     */
    public function getEan();

    /**
     * @return int|null
     */
    public function getDeliveryDate();

    /**
     * @return string|null
     */
    public function getManufacturer();

    /**
     * @return string|null
     */
    public function getCategoryText();

    /**
     * @return string[]
     */
    public function getParametersByName();

    /**
     * @return string|null
     */
    public function getPartno();

    /**
     * @return int|null
     */
    public function getMainVariantId();

    /**
     * @param string $name
     * @return mixed
     */
    public function getCustomValue($name);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setCustomValue($name, $value);
}
