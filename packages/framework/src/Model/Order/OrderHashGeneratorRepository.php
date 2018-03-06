<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Component\String\HashGenerator;

class OrderHashGeneratorRepository
{
    const HASH_LENGTH = 50;
    const MAX_GENERATE_TRIES = 100;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\String\HashGenerator
     */
    private $hashGenerator;

    public function __construct(
        OrderRepository $orderRepository,
        HashGenerator $hashGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->hashGenerator = $hashGenerator;
    }

    /**
     * @return string
     */
    public function getUniqueHash()
    {
        $triesCount = 0;
        do {
            $hash = $this->hashGenerator->generateHash(self::HASH_LENGTH);
            $order = $this->orderRepository->findByUrlHashIncludingDeletedOrders($hash);
            $triesCount++;
            if ($triesCount > self::MAX_GENERATE_TRIES) {
                throw new \Shopsys\FrameworkBundle\Model\Order\Exception\OrderHashGenerateException('Trying generate hash reached the limit.');
            }
        } while ($order !== null);
        return $hash;
    }
}
