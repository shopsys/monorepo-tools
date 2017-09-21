<?php

namespace Shopsys\ShopBundle\Form\Front\Order;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DomainAwareOrderFlowFactory
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        ContainerInterface $container,
        Domain $domain
    ) {
        $this->container = $container;
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Front\Order\OrderFlow
     */
    public function create()
    {
        $orderFlow = new OrderFlow();
        $orderFlow->setDomainId($this->domain->getId());

        // see vendor/craue/formflow-bundle/Resources/config/form_flow.xml
        $orderFlow->setDataManager($this->container->get('craue.form.flow.data_manager'));
        $orderFlow->setFormFactory($this->container->get('form.factory'));
        $orderFlow->setRequestStack($this->container->get('request_stack'));
        $orderFlow->setEventDispatcher($this->container->get('event_dispatcher'));

        return $orderFlow;
    }
}
