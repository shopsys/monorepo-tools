<?php

namespace Shopsys\ShopBundle\Model\Product\Flag;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Product\Flag\FlagData;
use Shopsys\ShopBundle\Model\Product\Flag\FlagRepository;
use Shopsys\ShopBundle\Model\Product\Flag\FlagService;

class FlagFacade {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Flag\FlagRepository
     */
    private $flagRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Flag\FlagService
     */
    private $flagService;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagRepository $flagRepository
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagService $flagService
     */
    public function __construct(
        EntityManager $em,
        FlagRepository $flagRepository,
        FlagService $flagService
    ) {
        $this->em = $em;
        $this->flagRepository = $flagRepository;
        $this->flagService = $flagService;
    }

    /**
     * @param int $flagId
     * @return \Shopsys\ShopBundle\Model\Product\Flag\Flag
     */
    public function getById($flagId) {
        return $this->flagRepository->getById($flagId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\ShopBundle\Model\Product\Flag\Flag
     */
    public function create(FlagData $flagData) {
        $flag = $this->flagService->create($flagData);
        $this->em->persist($flag);
        $this->em->flush();

        return $flag;
    }

    /**
     * @param int $flagId
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\ShopBundle\Model\Product\Flag\Flag
     */
    public function edit($flagId, FlagData $flagData) {
        $flag = $this->flagRepository->getById($flagId);
        $this->flagService->edit($flag, $flagData);
        $this->em->flush();

        return $flag;
    }

    /**
     * @param int $flagId
     */
    public function deleteById($flagId) {
        $flag = $this->flagRepository->getById($flagId);

        $this->em->remove($flag);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Flag\Flag[]
     */
    public function getAll() {
        return $this->flagRepository->getAll();
    }

}
