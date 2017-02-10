<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_SimpleFunction;

class ShopInfoExtension extends \Twig_Extension {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Shopsys\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade
     */
    private $shopInfoSettingFacade;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Shopsys\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade $shopInfoSettingFacade
     */
    public function __construct(
        ContainerInterface $container,
        ShopInfoSettingFacade $shopInfoSettingFacade
    ) {
        $this->container = $container;
        $this->shopInfoSettingFacade = $shopInfoSettingFacade;
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new Twig_SimpleFunction('getShopInfoPhoneNumber', [$this, 'getPhoneNumber']),
            new Twig_SimpleFunction('getShopInfoEmail', [$this, 'getEmail']),
            new Twig_SimpleFunction('getShopInfoPhoneHours', [$this, 'getPhoneHours']),
        ];
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private function getDomain() {
        // Twig extensions are loaded during assetic:dump command,
        // so they cannot be dependent on Domain service
        return $this->container->get(Domain::class);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'shopInfo';
    }

    /**
     * @return string
     */
    public function getPhoneNumber() {
        $currentDomainId = $this->getDomain()->getId();
        return $this->shopInfoSettingFacade->getPhoneNumber($currentDomainId);
    }

    /**
     * @return string
     */
    public function getEmail() {
        $currentDomainId = $this->getDomain()->getId();
        return $this->shopInfoSettingFacade->getEmail($currentDomainId);
    }

    /**
     * @return string
     */
    public function getPhoneHours() {
        $currentDomainId = $this->getDomain()->getId();
        return $this->shopInfoSettingFacade->getPhoneHours($currentDomainId);
    }
}
