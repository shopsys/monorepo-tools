<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Statistics;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;

class StatisticsRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPoint[]
     */
    public function getCustomersRegistrationsCountByDayBetweenTwoDateTimes(DateTime $start, DateTime $end): array
    {
        $resultSetMapping = new ResultSetMapping();
        $resultSetMapping->addScalarResult('count', 'count');
        $resultSetMapping->addScalarResult('date', 'date', Type::DATE);

        $query = $this->em->createNativeQuery(
            'SELECT DATE(u.created_at) AS date, COUNT(u.created_at) AS count
            FROM users u
            WHERE u.created_at BETWEEN :start_date AND :end_date
            GROUP BY date
            ORDER BY date ASC',
            $resultSetMapping
        );

        $query->setParameter('start_date', $start);
        $query->setParameter('end_date', $end);

        return array_map(
            function (array $item) {
                return new ValueByDateTimeDataPoint($item['count'], $item['date']);
            },
            $query->getResult()
        );
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Shopsys\FrameworkBundle\Model\Statistics\ValueByDateTimeDataPoint[]
     */
    public function getNewOrdersCountByDayBetweenTwoDateTimes(DateTime $start, DateTime $end): array
    {
        $resultSetMapping = new ResultSetMapping();
        $resultSetMapping->addScalarResult('count', 'count');
        $resultSetMapping->addScalarResult('date', 'date', Type::DATE);

        $query = $this->em->createNativeQuery(
            'SELECT DATE(o.created_at) AS date, COUNT(o.created_at) AS count
            FROM orders o
            WHERE o.created_at BETWEEN :start_date AND :end_date
            GROUP BY date
            ORDER BY date ASC',
            $resultSetMapping
        );

        $query->setParameter('start_date', $start);
        $query->setParameter('end_date', $end);

        return array_map(
            function (array $item) {
                return new ValueByDateTimeDataPoint($item['count'], $item['date']);
            },
            $query->getResult()
        );
    }

    /**
     * @param \DateTimeImmutable $startDateTime
     * @param \DateTimeImmutable $endDateTime
     * @return int
     */
    public function getNewCustomersCountBetweenDates(DateTimeImmutable $startDateTime, DateTimeImmutable $endDateTime): int
    {
        $resultSetMapping = new ResultSetMapping();
        $resultSetMapping->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery(
            'SELECT COUNT(u.created_at) AS count
            FROM users u
            WHERE u.created_at BETWEEN :start_date AND :end_date',
            $resultSetMapping
        );

        $query->setParameter('start_date', $startDateTime);
        $query->setParameter('end_date', $endDateTime);

        return (int)$query->getSingleScalarResult();
    }

    /**
     * @param \DateTimeImmutable $startDateTime
     * @param \DateTimeImmutable $endDateTime
     * @return int
     */
    public function getOrdersCountBetweenDates(DateTimeImmutable $startDateTime, DateTimeImmutable $endDateTime): int
    {
        $resultSetMapping = new ResultSetMapping();
        $resultSetMapping->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery(
            'SELECT COUNT(o.created_at) AS count
            FROM orders o
            WHERE o.created_at BETWEEN :start_date AND :end_date AND o.status_id != :canceled',
            $resultSetMapping
        );

        $query->setParameter('start_date', $startDateTime);
        $query->setParameter('end_date', $endDateTime);
        $query->setParameter('canceled', OrderStatus::TYPE_CANCELED);

        return (int)$query->getSingleScalarResult();
    }

    /**
     * @param \DateTimeImmutable $startDateTime
     * @param \DateTimeImmutable $endDateTime
     * @return int
     */
    public function getOrdersValueBetweenDates(DateTimeImmutable $startDateTime, DateTimeImmutable $endDateTime): int
    {
        $resultSetMapping = new ResultSetMapping();
        $resultSetMapping->addScalarResult('total_price', 'total_price');

        $query = $this->em->createNativeQuery(
            'SELECT SUM(o.total_price_with_vat * c.exchange_rate) AS total_price
            FROM orders o, currencies c
            WHERE o.created_at BETWEEN :start_date AND :end_date AND o.status_id != :canceled
            AND o.currency_id = c.id',
            $resultSetMapping
        );

        $query->setParameter('start_date', $startDateTime);
        $query->setParameter('end_date', $endDateTime);
        $query->setParameter('canceled', OrderStatus::TYPE_CANCELED);

        return (int)$query->getSingleScalarResult();
    }
}
