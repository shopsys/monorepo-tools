<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Command\Exception\DifferentTimezonesException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckTimezonesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:check-timezones')
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

        $connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        /* @var $connection \Doctrine\DBAL\Connection */

        $stmt = $connection->executeQuery('SHOW timezone');

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
