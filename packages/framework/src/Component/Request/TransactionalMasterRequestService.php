<?php

namespace Shopsys\FrameworkBundle\Component\Request;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class TransactionalMasterRequestService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var bool
     */
    private $inTransaction;

    public function __construct(EntityManager $em)
    {
        $this->inTransaction = false;
        $this->em = $em;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest() && !$this->inTransaction) {
            $this->em->beginTransaction();
            $this->inTransaction = true;
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->isMasterRequest() && $this->inTransaction) {
            $this->em->commit();
            $this->inTransaction = false;
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->isMasterRequest() && $this->inTransaction) {
            $this->em->rollback();
            $this->inTransaction = false;
        }
    }
}
