<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Pricing\Vat\VatService;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Setting\Setting;

class VatFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatRepository
	 */
	private $vatRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatService
	 */
	private $vatService;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatRepository $vatRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatService $vatService
	 * @param \SS6\ShopBundle\Model\Setting\Setting $setting
	 */
	public function __construct(
		EntityManager $em,
		VatRepository $vatRepository,
		VatService $vatService,
		Setting $setting
	) {
		$this->em = $em;
		$this->vatRepository = $vatRepository;
		$this->vatService = $vatService;
		$this->setting = $setting;
	}

	/**
	 * @param int $vatId
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getById($vatId) {
		return $this->vatRepository->getById($vatId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatData $vatData
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function create(VatData $vatData) {
		$vat = $this->vatService->create($vatData);
		$this->em->persist($vat);
		$this->em->flush();

		return $vat;
	}

	/**
	 * @param int $vatId
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatData $vatData
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function edit($vatId, VatData $vatData) {
		$vat = $this->vatRepository->getById($vatId);
		$this->vatService->edit($vat, $vatData);
		$this->em->flush();

		return $vat;
	}

	/**
	 * @param int $vatId
	 */
	public function deleteById($vatId) {
		$vat = $this->vatRepository->getById($vatId);
		
		$this->em->remove($vat);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getDefaultVat() {
		$defaultVatId = $this->setting->get(Vat::SETTING_DEFAULT_VAT);

		return $this->vatRepository->getById($defaultVatId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function setDefaultVat(Vat $vat) {
		$this->setting->set(Vat::SETTING_DEFAULT_VAT, $vat->getId());
	}

}
