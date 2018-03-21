<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderRepository;
use Shopsys\FrameworkBundle\Model\Order\OrderService;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class OrderItemFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderService
     */
    private $orderService;

    public function __construct(
        EntityManagerInterface $em,
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
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct
     */
    public function createOrderProductInOrder($orderId, $productId)
    {
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
