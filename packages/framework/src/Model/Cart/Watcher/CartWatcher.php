<?php

namespace Shopsys\FrameworkBundle\Model\Cart\Watcher;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository;

class CartWatcher
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    protected $productPriceCalculationForUser;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository
     */
    protected $productVisibilityRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
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
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[]
     */
    public function getModifiedPriceItemsAndUpdatePrices(Cart $cart)
    {
        $modifiedItems = [];
        foreach ($cart->getItems() as $cartItem) {
            $productPrice = $this->productPriceCalculationForUser->calculatePriceForCurrentUser($cartItem->getProduct());
            if (!$productPrice->getPriceWithVat()->equals(Money::fromValue($cartItem->getWatchedPrice()))) {
                $modifiedItems[] = $cartItem;
            }
            $cartItem->setWatchedPrice($productPrice->getPriceWithVat()->toValue());
        }
        return $modifiedItems;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     * @param \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer $currentCustomer
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[]
     */
    public function getNotListableItems(Cart $cart, CurrentCustomer $currentCustomer)
    {
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
            } catch (\Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException $e) {
                $notListableItems[] = $item;
            }
        }

        return $notListableItems;
    }
}
