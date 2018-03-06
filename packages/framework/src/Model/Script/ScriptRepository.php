<?php

namespace Shopsys\FrameworkBundle\Model\Script;

use Doctrine\ORM\EntityManager;

class ScriptRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getScriptRepository()
    {
        return $this->em->getRepository(Script::class);
    }

    /**
     * @param int $scriptId
     * @return \Shopsys\FrameworkBundle\Model\Script\Script
     */
    public function getById($scriptId)
    {
        $script = $this->getScriptRepository()->find($scriptId);

        if ($script === null) {
            throw new \Shopsys\FrameworkBundle\Model\Script\Exception\ScriptNotFoundException('Script with ID ' . $scriptId . ' does not exist.');
        }

        return $script;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Script\Script[]
     */
    public function getAll()
    {
        return $this->getScriptRepository()->findAll();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllQueryBuilder()
    {
        return $this->getScriptRepository()->createQueryBuilder('s');
    }

    /**
     * @param string $placement
     * @return \Shopsys\FrameworkBundle\Model\Script\Script[]
     */
    public function getScriptsByPlacement($placement)
    {
        return $this->getScriptRepository()->findBy(['placement' => $placement]);
    }
}
