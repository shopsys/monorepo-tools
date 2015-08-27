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
	 * @var string|null
	 */
	private $manufacturer;

	/**
	 * @var string|null
	 */
	private $categorytext;

	/**
	 * @var string[paramName]
	 */
	private $params;

	/**
	 * @var string|null
	 */
	private $cpc;

	/**
	 * @param int $itemId
	 * @param string $productname
	 * @param string $description
	 * @param string $url
	 * @param string|null $imgurl
	 * @param string $priceVat
	 * @param string|null $ean
	 * @param int|null $deliveryDate
	 * @param string|null $manufacturer
	 * @param string|null $categorytext
	 * @param string[paramName] $params
	 * @param string|null $cpc
	 */
	public function __construct(
		$itemId,
		$productname,
		$description,
		$url,
		$imgurl,
		$priceVat,
		$ean,
		$deliveryDate,
		$manufacturer,
		$categorytext,
		$params,
		$cpc
	) {
		$this->itemId = $itemId;
		$this->productname = $productname;
		$this->description = $description;
		$this->url = $url;
		$this->imgurl = $imgurl;
		$this->priceVat = $priceVat;
		$this->ean = $ean;
		$this->deliveryDate = $deliveryDate;
		$this->manufacturer = $manufacturer;
		$this->categorytext = $categorytext;
		$this->params = $params;
		$this->cpc = $cpc;
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

	/**
	 * @return string|null
	 */
	public function getManufacturer() {
		return $this->manufacturer;
	}

	/**
	 * @return string|null
	 */
	public function getCategorytext() {
		return $this->categorytext;
	}

	/**
	 * @return string[paramName]
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @return string|null
	 */
	public function getCpc() {
		return $this->cpc;
	}

}
