<?php

namespace Shopsys\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDumpCommand extends ContainerAwareCommand
{
    const ARG_OUTPUT_FILE = 'outputFile';
    const OPT_PGDUMP_BIN = 'pgdump-bin';

    /**
     * {@inheritDoc}
     */
    protected function configure() {
        $this
            ->setName('shopsys:database:dump')
            ->setDescription('Dump database')
            ->addArgument(self::ARG_OUTPUT_FILE, InputArgument::REQUIRED, 'Output SQL file')
            ->addOption(self::OPT_PGDUMP_BIN, null, InputOption::VALUE_OPTIONAL, 'Path to pg_dump binary', 'pg_dump');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        /* @var $connection \Doctrine\DBAL\Connection */

        $command = sprintf(
            '%s --dbname=%s --username=%s --no-password',
            escapeshellcmd($input->getOption(self::OPT_PGDUMP_BIN)),
            escapeshellarg($connection->getDatabase()),
            escapeshellarg($connection->getUsername()),
            escapeshellarg($input->getArgument(self::ARG_OUTPUT_FILE))
        );

        putenv('PGPASSWORD=' . $connection->getPassword());

        $pipes = [];
        $process = proc_open(
            $command,
            $this->getDescriptorSpec(),
            $pipes
        );

        list($stdin, $stdout, $stderr) = $pipes;

        $outputFile = $input->getArgument(self::ARG_OUTPUT_FILE);
        $outputFileHandle = fopen($outputFile, 'w');

        while (!feof($stdout)) {
            $line = fgets($stdout);
            fwrite($outputFileHandle, $line);
        }

        $errorMessage = stream_get_contents($stderr);
        if (strlen($errorMessage) > 0) {
            $output->writeln('<error>' . $errorMessage . '</error>');
        } else {
            $output->writeln(sprintf(
                'Database "%s" dumped into file: %s',
                $connection->getDatabase(),
                $outputFile
            ));
        }

        fclose($outputFileHandle);
        fclose($stdin);
        fclose($stdout);
        fclose($stderr);

        return proc_close($process);
    }

    /**
     * @return array
     */
    private function getDescriptorSpec() {
        return [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];
    }
}
