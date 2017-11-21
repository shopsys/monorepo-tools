<?php

namespace Shopsys\ShopBundle\Model\Cart\Item;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;
use Shopsys\ShopBundle\Model\Product\Product;

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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=127)
     */
    private $cartIdentifier;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\User|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Customer\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable = true, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Product\Product")
     * @ORM\JoinColumn(nullable=true, name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var string|null
     *
     * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
     */
    private $watchedPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $addedAt;

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $quantity
     * @param string $watchedPrice
     */
    public function __construct(
        CustomerIdentifier $customerIdentifier,
        Product $product,
        $quantity,
        $watchedPrice
    ) {
        $this->cartIdentifier = $customerIdentifier->getCartIdentifier();
        $this->user = $customerIdentifier->getUser();
        $this->product = $product;
        $this->watchedPrice = $watchedPrice;
        $this->changeQuantity($quantity);
        $this->addedAt = new DateTime();
    }

    /**
     * @param int $newQuantity
     */
    public function changeQuantity($newQuantity)
    {
        if (filter_var($newQuantity, FILTER_VALIDATE_INT) === false || $newQuantity <= 0) {
            throw new \Shopsys\ShopBundle\Model\Cart\Exception\InvalidQuantityException($newQuantity);
        }

        $this->quantity = $newQuantity;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getProduct()
    {
        if ($this->product === null) {
            throw new \Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException();
        }

        return $this->product;
    }

    /**
     * @param string|null $locale
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getName($locale = null)
    {
        return $this->getProduct()->getName($locale);
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return string|null
     */
    public function getWatchedPrice()
    {
        return $this->watchedPrice;
    }

    /**
     * @param string|null $watchedPrice
     */
    public function setWatchedPrice($watchedPrice)
    {
        $this->watchedPrice = $watchedPrice;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Item\CartItem $cartItem
     * @return bool
     */
    public function isSimilarItemAs(self $cartItem)
    {
        return $this->getProduct()->getId() === $cartItem->getProduct()->getId();
    }

    /**
     * @return string
     */
    public function getCartIdentifier()
    {
        return $this->cartIdentifier;
    }

    /**
     * @return \DateTime
     */
    public function getAddedAt()
    {
        return $this->addedAt;
    }

    /**
     * @param \DateTime $addedAt
     */
    public function changeAddedAt(DateTime $addedAt)
    {
        $this->addedAt = $addedAt;
    }
}
