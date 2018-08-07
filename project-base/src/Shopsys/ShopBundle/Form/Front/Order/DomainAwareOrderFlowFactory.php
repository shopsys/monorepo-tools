<?php

namespace Shopsys\ShopBundle\Form\Front\Order;

use Craue\FormFlowBundle\Storage\DataManager;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Order\OrderFlowFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DomainAwareOrderFlowFactory implements OrderFlowFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Craue\FormFlowBundle\Storage\DataManager
     */
    private $dataManager;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        Domain $domain,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        FormFactoryInterface $formFactory,
        DataManager $dataManager
    ) {
        $this->domain = $domain;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
        $this->dataManager = $dataManager;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Front\Order\OrderFlow
     */
    public function create()
    {
        $orderFlow = new OrderFlow();
        $orderFlow->setDomainId($this->domain->getId());

        // see vendor/craue/formflow-bundle/Resources/config/form_flow.xml
        $orderFlow->setDataManager($this->dataManager);
        $orderFlow->setFormFactory($this->formFactory);
        $orderFlow->setRequestStack($this->requestStack);
        $orderFlow->setEventDispatcher($this->eventDispatcher);

        return $orderFlow;
    }
}
