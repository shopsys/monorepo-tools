<?php

namespace Shopsys\MigrationBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckOrmMappingCommand extends Command
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:migrations:check-mapping';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Check if ORM mapping is valid');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checking ORM mapping...');

        $schemaValidator = new SchemaValidator($this->em);
        $schemaErrors = $schemaValidator->validateMapping();

        if (count($schemaErrors) > 0) {
            foreach ($schemaErrors as $className => $classErrors) {
                $output->writeln('<error>The entity-class ' . $className . ' mapping is invalid:</error>');

                foreach ($classErrors as $classError) {
                    $output->writeln('<error>- ' . $classError . '</error>');
                }

                $output->writeln('');
            }

            return self::RETURN_CODE_ERROR;
        }

        $output->writeln('<info>ORM mapping is valid.</info>');

        return self::RETURN_CODE_OK;
    }
}
