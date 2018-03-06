<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;

class AdvertFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Advert\AdvertRepository
     */
    private $advertRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertRepository $advertRepository
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain
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
     * @return \Shopsys\FrameworkBundle\Model\Advert\Advert
     */
    public function getById($advertId)
    {
        return $this->advertRepository->getById($advertId);
    }

    /**
     * @param string $positionName
     * @return \Shopsys\FrameworkBundle\Model\Advert\Advert|null
     */
    public function findRandomAdvertByPositionOnCurrentDomain($positionName)
    {
        return $this->advertRepository->findRandomAdvertByPosition($positionName, $this->domain->getId());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertData $advertData
     * @return \Shopsys\FrameworkBundle\Model\Advert\Advert
     */
    public function create(AdvertData $advertData)
    {
        $advert = new Advert($advertData);

        $this->em->persist($advert);
        $this->em->flush();
        $this->imageFacade->uploadImage($advert, $advertData->image, null);
        $this->em->flush();

        return $advert;
    }

    /**
     * @param int $advertId
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertData $advertData
     * @return \Shopsys\FrameworkBundle\Model\Advert\Advert
     */
    public function edit($advertId, AdvertData $advertData)
    {
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
    public function delete($advertId)
    {
        $advert = $this->advertRepository->getById($advertId);
        $this->em->remove($advert);
        $this->em->flush();
    }
}
