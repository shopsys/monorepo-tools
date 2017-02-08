<?php

namespace SS6\ShopBundle\Model\Advert;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Model\Advert\AdvertRepository;

class AdvertEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertRepository
	 */
	private $advertRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Advert\AdvertRepository $advertRepository
	 * @param \SS6\ShopBundle\Component\Image\ImageFacade $imageFacade
	 * @param \SS6\ShopBundle\Component\Domain\Domain
	 */
	public function __construct(
		EntityManager $em,
		AdvertRepository $advertRepository,
		ImageFacade $imageFacade,
		Domain $domain
	) {
		$this->em = $em;
		$this->advertRepository = $advertRepository;
		$this->imageFacade = $imageFacade;
		$this->domain = $domain;
	}

	/**
	 * @param int $advertId
	 * @return \SS6\ShopBundle\Model\Advert\Advert
	 */
	public function getById($advertId) {
		return $this->advertRepository->getById($advertId);
	}

	/**
	 * @param string $positionName
	 * @return \SS6\ShopBundle\Model\Advert\Advert|null
	 */
	public function findRandomAdvertByPositionOnCurrentDomain($positionName) {
		return $this->advertRepository->findRandomAdvertByPosition($positionName, $this->domain->getId());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Advert\AdvertData $advertData
	 * @return \SS6\ShopBundle\Model\Advert\Advert
	 */
	public function create(AdvertData $advertData) {
		$advert = new Advert($advertData);

		$this->em->persist($advert);
		$this->em->flush();
		$this->imageFacade->uploadImage($advert, $advertData->image, null);
		$this->em->flush();

		return $advert;
	}

	/**
	 * @param int $advertId
	 * @param \SS6\ShopBundle\Model\Advert\AdvertData $advertData
	 * @return \SS6\ShopBundle\Model\Advert\Advert
	 */
	public function edit($advertId, AdvertData $advertData) {
		$advert = $this->advertRepository->getById($advertId);
		$advert->edit($advertData);

		$this->em->flush();
		$this->imageFacade->uploadImage($advert, $advertData->image, null);
		$this->em->flush();

		return $advert;
	}

	/**
	 * @param int $advertId
	 */
	public function delete($advertId) {
		$advert = $this->advertRepository->getById($advertId);
		$this->em->remove($advert);
		$this->em->flush();
	}

}
