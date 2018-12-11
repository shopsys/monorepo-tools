<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\OrderRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class OrderItemFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    protected $productPriceCalculationForUser;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation
     */
    protected $orderPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface
     */
    protected $orderProductFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderRepository $orderRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation $orderPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface $orderProductFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        Domain $domain,
        OrderPriceCalculation $orderPriceCalculation,
        OrderProductFactoryInterface $orderProductFactory
    ) {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->domain = $domain;
        $this->orderPriceCalculation = $orderPriceCalculation;
        $this->orderProductFactory = $orderProductFactory;
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct
     */
    public function addProductToOrder($orderId, $productId)
    {
        $order = $this->orderRepository->getById($orderId);
        $product = $this->productRepository->getById($productId);

        $productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
            $product,
            $order->getDomainId(),
            $order->getCustomer()
        );

        $orderProduct = $order->addProduct(
            $product,
            $productPrice,
            $this->orderProductFactory,
            $this->domain,
            $this->orderPriceCalculation
        );

        $this->em->flush($order);

        return $orderProduct;
    }
}
