<?php

namespace Shopsys\ShopBundle\Model\Breadcrumb;

use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem;

/**
 * @return \Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem
 */
class FrontBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    /**
     * @param string $routeName
     * @param array $routeParameters
     * @return \Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = [])
    {
        switch ($routeName) {
            case 'front_customer_edit':
                return [
                    new BreadcrumbItem(t('Edit data')),
                ];
            case 'front_customer_orders':
                return [
                    new BreadcrumbItem(t('Orders')),
                ];
            case 'front_registration_reset_password':
                return [
                    new BreadcrumbItem(t('Forgotten password')),
                ];
            case 'front_customer_order_detail_registered':
            case 'front_customer_order_detail_unregistered':
                return [
                    new BreadcrumbItem(t('Order detail')),
                ];
            case 'front_login':
                return [
                    new BreadcrumbItem(t('Login')),
                ];
            case 'front_product_search':
                return [
                    new BreadcrumbItem(t('Search [noun]')),
                ];
            case 'front_registration_register':
                return [
                    new BreadcrumbItem(t('Registration')),
                ];
            case 'front_brand_list':
                return [
                    new BreadcrumbItem(t('Brand overview')),
                ];
            case 'front_error_page':
            case 'front_error_page_format':
                return $this->getBreadcrumbItemForErrorPage($routeParameters['code']);
        }
    }

    /**
     * @param string $code
     * @return \Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
     */
    private function getBreadcrumbItemForErrorPage($code)
    {
        $isPageNotFound = $code === '404';
        $breadcrumbName = $isPageNotFound ? t('Page not found') : t('Oops! Error occurred');

        return [
            new BreadcrumbItem($breadcrumbName),
        ];
    }

    /**
     * @return string[]
     */
    public function getRouteNames()
    {
        return [
            'front_customer_edit',
            'front_customer_orders',
            'front_registration_reset_password',
            'front_customer_order_detail_registered',
            'front_customer_order_detail_unregistered',
            'front_login',
            'front_product_search',
            'front_registration_register',
            'front_brand_list',
            'front_customer_edit',
            'front_error_page',
            'front_error_page_format',
        ];
    }
}
