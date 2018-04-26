<?php

namespace Shopsys\FrameworkBundle\Model\Module;

use Doctrine\ORM\EntityManagerInterface;

class ModuleFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Module\EnabledModuleRepository
     */
    protected $enabledModuleRepository;

    public function __construct(
        EntityManagerInterface $em,
        EnabledModuleRepository $enabledModuleRepository
    ) {
        $this->em = $em;
        $this->enabledModuleRepository = $enabledModuleRepository;
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    public function isEnabled($moduleName)
    {
        $enabledModule = $this->enabledModuleRepository->findByName($moduleName);

        return $enabledModule !== null;
    }

    /**
     * @param string $moduleName
     * @param bool $isEnabled
     */
    public function setEnabled($moduleName, $isEnabled)
    {
        $enabledModule = $this->enabledModuleRepository->findByName($moduleName);

        if ($enabledModule === null && $isEnabled) {
            $enabledModule = new EnabledModule($moduleName);
            $this->em->persist($enabledModule);
        } elseif ($enabledModule !== null && !$isEnabled) {
            $this->em->remove($enabledModule);
        }

        $this->em->flush();
    }
}
