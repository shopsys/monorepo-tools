<?php

namespace ShopSys\MigrationBundle\Command;

use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckOrmMappingCommand extends ContainerAwareCommand
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_ERROR = 1;

    protected function configure()
    {
        $this
            ->setName('shopsys:migrations:check-mapping')
            ->setDescription('Check if ORM mapping is valid');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em \Doctrine\ORM\EntityManager */

        $output->writeln('Checking ORM mapping...');

        $schemaValidator = new SchemaValidator($em);
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
