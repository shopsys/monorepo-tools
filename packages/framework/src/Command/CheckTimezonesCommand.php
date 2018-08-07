<?php

namespace Shopsys\FrameworkBundle\Command;

use Doctrine\DBAL\Connection;
use Shopsys\FrameworkBundle\Command\Exception\DifferentTimezonesException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckTimezonesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:check-timezones';

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Checks uniformity of PHP and Postgres timezones');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkUniformityOfTimezones($output);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function checkUniformityOfTimezones(OutputInterface $output)
    {
        $output->writeln('Checks uniformity of PHP and Postgres timezones...');

        $phpTimezone = empty(ini_get('date.timezone')) ? date_default_timezone_get() : ini_get('date.timezone');

        $stmt = $this->connection->executeQuery('SHOW timezone');

        $postgreSqlTimezone = $stmt->fetchColumn();

        if ($postgreSqlTimezone !== $phpTimezone) {
            $message = sprintf(
                'Timezones in PHP and database configuration must be identical.'
                . ' Current settings - PHP:%s, PostgreSQL:%s',
                $phpTimezone,
                $postgreSqlTimezone
            );
            throw new DifferentTimezonesException($message);
        }

        $output->writeln('Timezones in PHP and database configuration are identical');
    }
}
