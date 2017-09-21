<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Symfony\Component\HttpFoundation\Session\Session;

class AdminDomainTabsFacade
{
    const SESSION_SELECTED_DOMAIN = 'selected_domain_id';

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(Domain $domain, Session $session)
    {
        $this->domain = $domain;
        $this->session = $session;
    }

    public function getSelectedDomainId()
    {
        return $this->getSelectedDomainConfig()->getId();
    }

    /**
     * @param int $domainId
     */
    public function setSelectedDomainId($domainId)
    {
        $domainConfig = $this->domain->getDomainConfigById($domainId);
        $this->session->set(self::SESSION_SELECTED_DOMAIN, $domainConfig->getId());
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig
     */
    public function getSelectedDomainConfig()
    {
        try {
            $domainId = $this->session->get(self::SESSION_SELECTED_DOMAIN);
            return $this->domain->getDomainConfigById($domainId);
        } catch (\Shopsys\ShopBundle\Component\Domain\Exception\InvalidDomainIdException $e) {
            $allDomains = $this->domain->getAll();
            return reset($allDomains);
        }
    }
}
