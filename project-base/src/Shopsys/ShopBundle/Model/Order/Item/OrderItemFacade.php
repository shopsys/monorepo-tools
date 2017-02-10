<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Order\OrderRepository;
use Shopsys\ShopBundle\Model\Order\OrderService;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class OrderItemFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderService
     */
    private $orderService;

    public function __construct(
        EntityManager $em,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        OrderService $orderService
    ) {
        $this->em = $em;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->orderService = $orderService;
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @return \Shopsys\ShopBundle\Model\Order\Item\OrderProduct
     */
    public function createOrderProductInOrder($orderId, $productId) {
        $order = $this->orderRepository->getById($orderId);
        $product = $this->productRepository->getById($productId);

        $productPrice = $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
            $product,
            $order->getDomainId(),
            $order->getCustomer()
        );

        $orderProduct = $this->orderService->createOrderProductInOrder($order, $product, $productPrice);

        $this->em->persist($orderProduct);
        $this->em->flush([
            $order,
            $orderProduct,
        ]);

        return $orderProduct;
    }
}
