<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

class SqlLoggerFacade
{
    /**
     * @var \Doctrine\DBAL\Logging\SQLLogger|null
     */
    private $sqlLogger;

    /**
     * @var bool
     */
    private $isLoggerTemporarilyDisabled;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->isLoggerTemporarilyDisabled = false;
    }

    public function temporarilyDisableLogging()
    {
        if ($this->isLoggerTemporarilyDisabled) {
            $message = 'Trying to disable already disabled SQL logger.';
            throw new \Shopsys\FrameworkBundle\Component\Doctrine\Exception\SqlLoggerAlreadyDisabledException($message);
        }
        $this->sqlLogger = $this->em->getConnection()->getConfiguration()->getSQLLogger();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->isLoggerTemporarilyDisabled = true;
    }

    public function reenableLogging()
    {
        if (!$this->isLoggerTemporarilyDisabled) {
            $message = 'Trying to reenable already enabled SQL logger.';
            throw new \Shopsys\FrameworkBundle\Component\Doctrine\Exception\SqlLoggerAlreadyEnabledException($message);
        }
        $this->em->getConnection()->getConfiguration()->setSQLLogger($this->sqlLogger);
        $this->sqlLogger = null;
        $this->isLoggerTemporarilyDisabled = false;
    }
}
