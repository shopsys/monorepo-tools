<?php

namespace Shopsys\FrameworkBundle\Model\Breadcrumb;

use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbItem;

class SimpleBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    /**
     * @var string[]|null
     */
    private $routeNameMap;

    /**
     * @param string $routeName
     * @param array $routeParameters
     * @return \Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbItem[]
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = [])
    {
        $routeNameMap = $this->getRouteNameMap();

        return [
            new BreadcrumbItem($routeNameMap[$routeName]),
        ];
    }

    /**
     * @return string[]
     */
    public function getRouteNames()
    {
        return array_keys($this->getRouteNameMap());
    }

    /**
     * @return string[]
     */
    private function getRouteNameMap()
    {
        if ($this->routeNameMap === null) {
            // Caching in order to translate breadcrumb item names only once
            $this->routeNameMap = [
                'front_customer_edit' => t('Edit data'),
                'front_customer_orders' => t('Orders'),
                'front_registration_reset_password' => t('Forgotten password'),
                'front_customer_order_detail_registered' => t('Order detail'),
                'front_customer_order_detail_unregistered' => t('Order detail'),
                'front_login' => t('Login'),
                'front_product_search' => t('Search [noun]'),
                'front_registration_register' => t('Registration'),
                'front_brand_list' => t('Brand overview'),
            ];
        }

        return $this->routeNameMap;
    }
}
