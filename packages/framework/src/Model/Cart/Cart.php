<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;

/**
 * @ORM\Table(name="carts")
 * @ORM\Entity
 */
class Cart
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=127)
     */
    protected $cartIdentifier;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\User|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Customer\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable = true, onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Shopsys\FrameworkBundle\Model\Cart\Item\CartItem",
     *     mappedBy="cart",
     *     cascade={"remove"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $items;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $modifiedAt;

    /**
     * @param string $cartIdentifier
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     */
    public function __construct(string $cartIdentifier, ?User $user = null)
    {
        $this->cartIdentifier = $cartIdentifier;
        $this->user = $user;
        $this->items = new ArrayCollection();
        $this->modifiedAt = new DateTime();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem $item
     */
    public function addItem(CartItem $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $this->setModifiedNow();
        }
    }

    /**
     * @param int $itemId
     */
    public function removeItemById($itemId)
    {
        foreach ($this->items as $key => $item) {
            if ($item->getId() === $itemId) {
                $this->items->removeElement($item);
                $this->setModifiedNow();
                return;
            }
        }
        $message = 'Cart item with ID = ' . $itemId . ' is not in cart for remove.';
        throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidCartItemException($message);
    }

    public function clean()
    {
        $this->items->clear();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[]
     */
    public function getItems()
    {
        return $this->items->toArray();
    }

    /**
     * @return int
     */
    public function getItemsCount()
    {
        return $this->items->count();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->getItemsCount() === 0;
    }

    /**
     * @param array $quantitiesByItemId
     */
    public function changeQuantities(array $quantitiesByItemId)
    {
        foreach ($this->items as $item) {
            if (array_key_exists($item->getId(), $quantitiesByItemId)) {
                $item->changeQuantity($quantitiesByItemId[$item->getId()]);
            }
        }

        $this->setModifiedNow();
    }

    /**
     * @param int $itemId
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem
     */
    public function getItemById($itemId)
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $itemId) {
                return $item;
            }
        }
        $message = 'CartItem with id = ' . $itemId . ' not found in cart.';
        throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidCartItemException($message);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[]
     */
    public function getQuantifiedProducts()
    {
        $quantifiedProducts = [];
        foreach ($this->items as $item) {
            $quantifiedProducts[] = new QuantifiedProduct($item->getProduct(), $item->getQuantity());
        }

        return $quantifiedProducts;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cartToMerge
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     */
    public function mergeWithCart(self $cartToMerge, CartItemFactoryInterface $cartItemFactory)
    {
        foreach ($cartToMerge->getItems() as $itemToMerge) {
            $similarItem = $this->findSimilarItemByItem($itemToMerge);
            if ($similarItem instanceof CartItem) {
                $similarItem->changeQuantity($similarItem->getQuantity() + $itemToMerge->getQuantity());
            } else {
                $newCartItem = $cartItemFactory->create(
                    $this,
                    $itemToMerge->getProduct(),
                    $itemToMerge->getQuantity(),
                    $itemToMerge->getWatchedPrice()
                );
                $this->addItem($newCartItem);
            }
        }

        $this->setModifiedNow();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem $item
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem|null
     */
    protected function findSimilarItemByItem(CartItem $item)
    {
        foreach ($this->items as $similarItem) {
            if ($similarItem->isSimilarItemAs($item)) {
                return $similarItem;
            }
        }

        return null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $quantity
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     * @return \Shopsys\FrameworkBundle\Model\Cart\AddProductResult
     */
    public function addProduct(
        Product $product,
        $quantity,
        ProductPriceCalculationForUser $productPriceCalculation,
        CartItemFactoryInterface $cartItemFactory
    ) {
        if (!is_int($quantity) || $quantity <= 0) {
            throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidQuantityException($quantity);
        }

        foreach ($this->items as $item) {
            if ($item->getProduct() === $product) {
                $item->changeQuantity($item->getQuantity() + $quantity);
                $item->changeAddedAt(new DateTime());
                return new AddProductResult($item, false, $quantity);
            }
        }

        $productPrice = $productPriceCalculation->calculatePriceForCurrentUser($product);
        $newCartItem = $cartItemFactory->create($this, $product, $quantity, $productPrice->getPriceWithVat());
        $this->addItem($newCartItem);
        $this->setModifiedNow();

        return new AddProductResult($newCartItem, true, $quantity);
    }

    /**
     * @return string
     */
    public function getCartIdentifier()
    {
        return $this->cartIdentifier;
    }

    protected function setModifiedNow()
    {
        $this->modifiedAt = new DateTime();
    }

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt(DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }
}
