<?php

namespace Shopsys\ShopBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterData;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterService;

class ParameterFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository
     */
    private $parameterRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterService
     */
    private $parameterService;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository $parameterRepository
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterService $parameterService
     */
    public function __construct(
        EntityManager $em,
        ParameterRepository $parameterRepository,
        ParameterService $parameterService
    ) {
        $this->em = $em;
        $this->parameterRepository = $parameterRepository;
        $this->parameterService = $parameterService;
    }

    /**
     * @param int $parameterId
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public function getById($parameterId)
    {
        return $this->parameterRepository->getById($parameterId);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter[]
     */
    public function getAll()
    {
        return $this->parameterRepository->getAll();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public function create(ParameterData $parameterData)
    {
        $parameter = $this->parameterService->create($parameterData);
        $this->em->persist($parameter);
        $this->em->flush($parameter);

        return $parameter;
    }

    /**
     * @param string[] $namesByLocale
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter|null
     */
    public function findParameterByNames(array $namesByLocale)
    {
        return $this->parameterRepository->findParameterByNames($namesByLocale);
    }

    /**
     * @param int $parameterId
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    public function edit($parameterId, ParameterData $parameterData)
    {
        $parameter = $this->parameterRepository->getById($parameterId);
        $this->parameterService->edit($parameter, $parameterData);
        $this->em->flush();

        return $parameter;
    }

    /**
     * @param int $parameterId
     */
    public function deleteById($parameterId)
    {
        $parameter = $this->parameterRepository->getById($parameterId);

        $this->em->remove($parameter);
        $this->em->flush();
    }
}
