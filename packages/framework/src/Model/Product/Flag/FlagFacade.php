<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

use Doctrine\ORM\EntityManagerInterface;

class FlagFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagRepository
     */
    private $flagRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\FlagService
     */
    private $flagService;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagRepository $flagRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagService $flagService
     */
    public function __construct(
        EntityManagerInterface $em,
        FlagRepository $flagRepository,
        FlagService $flagService
    ) {
        $this->em = $em;
        $this->flagRepository = $flagRepository;
        $this->flagService = $flagService;
    }

    /**
     * @param int $flagId
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag
     */
    public function getById($flagId)
    {
        return $this->flagRepository->getById($flagId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag
     */
    public function create(FlagData $flagData)
    {
        $flag = $this->flagService->create($flagData);
        $this->em->persist($flag);
        $this->em->flush();

        return $flag;
    }

    /**
     * @param int $flagId
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $flagData
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag
     */
    public function edit($flagId, FlagData $flagData)
    {
        $flag = $this->flagRepository->getById($flagId);
        $this->flagService->edit($flag, $flagData);
        $this->em->flush();

        return $flag;
    }

    /**
     * @param int $flagId
     */
    public function deleteById($flagId)
    {
        $flag = $this->flagRepository->getById($flagId);

        $this->em->remove($flag);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public function getAll()
    {
        return $this->flagRepository->getAll();
    }
}
