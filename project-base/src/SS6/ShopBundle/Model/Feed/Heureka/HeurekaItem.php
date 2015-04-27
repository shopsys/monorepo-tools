<?php

namespace SS6\ShopBundle\Model\Feed\Heureka;

class HeurekaItem {

	/**
	 * @var int
	 */
	private $itemId;

	/**
	 * @var string
	 */
	private $productname;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var string|null
	 */
	private $imgurl;

	/**
	 * @var string
	 */
	private $priceVat;

	/**
	 * @var string|null
	 */
	private $ean;

	/**
	 * @var int|null
	 */
	private $deliveryDate;

	/**
	 * @param int $itemId
	 * @param string $productname
	 * @param string $description
	 * @param string $url
	 * @param string|null $imgurl
	 * @param string $priceVat
	 * @param string|null $ean
	 * @param int|null $deliveryDate
	 */
	public function __construct($itemId, $productname, $description, $url, $imgurl, $priceVat, $ean, $deliveryDate) {
		$this->itemId = $itemId;
		$this->productname = $productname;
		$this->description = $description;
		$this->url = $url;
		$this->imgurl = $imgurl;
		$this->priceVat = $priceVat;
		$this->ean = $ean;
		$this->deliveryDate = $deliveryDate;
	}

	/**
	 * @return int
	 */
	public function getItemId() {
		return $this->itemId;
	}

	/**
	 * @return string
	 */
	public function getProductname() {
		return $this->productname;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return string|null
	 */
	public function getImgurl() {
		return $this->imgurl;
	}

	/**
	 * @return string
	 */
	public function getPriceVat() {
		return $this->priceVat;
	}

	/**
	 * @return string|null
	 */
	public function getEan() {
		return $this->ean;
	}

	/**
	 * @return int|null
	 */
	public function getDeliveryDate() {
		return $this->deliveryDate;
	}

}
