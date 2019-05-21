<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Cart\Item;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Product\Product;

/**
 * @ORM\Table(name="cart_items")
 * @ORM\Entity
 */
class CartItem
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
     * @var \Shopsys\FrameworkBundle\Model\Cart\Cart
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Cart\Cart", inversedBy="items")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id", nullable=false)
     */
    protected $cart;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product")
     * @ORM\JoinColumn(nullable=true, name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $product;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     *
     * @ORM\Column(type="money", precision=20, scale=6, nullable=true)
     */
    protected $watchedPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $addedAt;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $quantity
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $watchedPrice
     */
    public function __construct(
        Cart $cart,
        Product $product,
        int $quantity,
        ?Money $watchedPrice
    ) {
        $this->cart = $cart;
        $this->product = $product;
        $this->setWatchedPrice($watchedPrice);
        $this->changeQuantity($quantity);
        $this->addedAt = new DateTime();
    }

    /**
     * @param int $newQuantity
     */
    public function changeQuantity(int $newQuantity): void
    {
        if (filter_var($newQuantity, FILTER_VALIDATE_INT) === false || $newQuantity <= 0) {
            throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidQuantityException($newQuantity);
        }

        $this->quantity = $newQuantity;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getProduct(): Product
    {
        if ($this->product === null) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException();
        }

        return $this->product;
    }

    /**
     * @param string|null $locale
     * @return string|null
     */
    public function getName(?string $locale = null): ?string
    {
        return $this->getProduct()->getName($locale);
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public function getWatchedPrice(): ?Money
    {
        return $this->watchedPrice;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $watchedPrice
     */
    public function setWatchedPrice(?Money $watchedPrice): void
    {
        $this->watchedPrice = $watchedPrice;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem $cartItem
     * @return bool
     */
    public function isSimilarItemAs(self $cartItem): bool
    {
        return $this->getProduct()->getId() === $cartItem->getProduct()->getId();
    }

    /**
     * @return \DateTime
     */
    public function getAddedAt(): DateTime
    {
        return $this->addedAt;
    }

    /**
     * @param \DateTime $addedAt
     */
    public function changeAddedAt(DateTime $addedAt): void
    {
        $this->addedAt = $addedAt;
    }
}
