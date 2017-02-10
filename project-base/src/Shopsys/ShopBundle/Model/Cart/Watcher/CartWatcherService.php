<?php

namespace Shopsys\ShopBundle\Model\Cart\Watcher;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Cart\Cart;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository;

class CartWatcherService
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository
     */
    private $productVisibilityRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
     * @param \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
     * @param \Shopsys\ShopBundle\Component\Domain\Domain
     */
    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        ProductVisibilityRepository $productVisibilityRepository,
        Domain $domain
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->productVisibilityRepository = $productVisibilityRepository;
        $this->domain = $domain;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     * @return \Shopsys\ShopBundle\Model\Cart\Item\CartItem[]
     */
    public function getModifiedPriceItemsAndUpdatePrices(Cart $cart) {
        $modifiedItems = [];
        foreach ($cart->getItems() as $cartItem) {
            $productPrice = $this->productPriceCalculationForUser->calculatePriceForCurrentUser($cartItem->getProduct());
            if ($cartItem->getWatchedPrice() != $productPrice->getPriceWithVat()) {
                $modifiedItems[] = $cartItem;
            }
            $cartItem->setWatchedPrice($productPrice->getPriceWithVat());
        }
        return $modifiedItems;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     * @param \Shopsys\ShopBundle\Model\Customer\CurrentCustomer $currentCustomer
     * @return \Shopsys\ShopBundle\Model\Cart\Item\CartItem[]
     */
    public function getNotListableItems(Cart $cart, CurrentCustomer $currentCustomer) {
        $notListableItems = [];
        foreach ($cart->getItems() as $item) {
            try {
                $product = $item->getProduct();
                $productVisibility = $this->productVisibilityRepository
                    ->getProductVisibility(
                        $product,
                        $currentCustomer->getPricingGroup(),
                        $this->domain->getId()
                    );

                if (!$productVisibility->isVisible() || $product->getCalculatedSellingDenied()) {
                    $notListableItems[] = $item;
                }
            } catch (\Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
                $notListableItems[] = $item;
            }
        }

        return $notListableItems;
    }
}
