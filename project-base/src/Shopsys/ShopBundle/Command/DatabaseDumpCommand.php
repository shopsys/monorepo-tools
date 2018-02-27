<?php

namespace Shopsys\ShopBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDumpCommand extends Command
{
    const ARG_OUTPUT_FILE = 'outputFile';
    const OPT_PGDUMP_BIN = 'pgdump-bin';

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:database:dump';

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

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Dump database')
            ->addArgument(self::ARG_OUTPUT_FILE, InputArgument::REQUIRED, 'Output SQL file')
            ->addOption(self::OPT_PGDUMP_BIN, null, InputOption::VALUE_OPTIONAL, 'Path to pg_dump binary', 'pg_dump');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // --schema=public option is used in order to dump only "public" schema which contains the application data
        // --no-owner option ensures that the dump can be imported on system with different database username
        $command = sprintf(
            '%s --host=%s --dbname=%s --no-owner --schema=public --username=%s --no-password',
            escapeshellcmd($input->getOption(self::OPT_PGDUMP_BIN)),
            escapeshellarg($this->connection->getHost()),
            escapeshellarg($this->connection->getDatabase()),
            escapeshellarg($this->connection->getUsername())
        );

        putenv('PGPASSWORD=' . $this->connection->getPassword());

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
                $this->connection->getDatabase(),
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
    private function getDescriptorSpec()
    {
        return [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];
    }
}
