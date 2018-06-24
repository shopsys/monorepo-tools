<?php

namespace Shopsys\FrameworkBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Sharding\PoolingShardConnection;
use Shopsys\FrameworkBundle\Component\DataFixture\FixturesLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * The class is copy-pasted from LoadDataFixturesDoctrineCommand from DoctrineFixutresBundle and adds the --fixtures
 * option that enables loading data fixtures from specified directories. We need it for our phing targets
 * (eg. test-db-fixtures-base-settings). The option is not supported in the original command in the new version of the bundle anymore.
 * Extending the original class is not possible because it requires final class \Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader
 * in constructor which can not be extended.
 * @see \Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand
 */
class LoadDataFixturesCommand extends DoctrineCommand
{

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\FixturesLoader
     */
    private $fixturesLoader;

    public function __construct(FixturesLoader $fixturesLoader)
    {
        parent::__construct();
        $this->fixturesLoader = $fixturesLoader;
    }

    protected function configure()
    {
        $this->setName('shopsys:fixtures:load')
           ->setDescription('Load data fixtures to your database.')
           ->addOption('append', null, InputOption::VALUE_NONE, 'Append the data fixtures instead of deleting all data from the database first.')
           ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')
           ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command.')
           ->addOption('purge-with-truncate', null, InputOption::VALUE_NONE, 'Purge data by using a database-level TRUNCATE statement')
           ->addOption('fixtures', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Define a directory to load the fixtures from')
           ->setHelp(
               <<<EOT
The <info>%command.name%</info> command loads data fixtures from your application:

  <info>php %command.full_name%</info>

Fixtures are services that are tagged with doctrine.fixture.orm.
 
If you want to append the fixtures instead of flushing the database first you can use the <info>--append</info> option:

  <info>php %command.full_name% --append</info>

By default Doctrine Data Fixtures uses DELETE statements to drop the existing rows from
the database. If you want to use a TRUNCATE statement instead you can use the <info>--purge-with-truncate</info> flag:

  <info>php %command.full_name% --purge-with-truncate</info>
EOT
           );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $em = $doctrine->getManager($input->getOption('em'));

        if ($input->isInteractive() && !$input->getOption('append')) {
            if (!$this->askConfirmation($input, $output, '<question>Careful, database will be purged. Do you want to continue y/N ?</question>', false)) {
                return;
            }
        }

        if ($input->getOption('shard')) {
            if (!$em->getConnection() instanceof PoolingShardConnection) {
                throw new \LogicException(sprintf("Connection of EntityManager '%s' must implement shards configuration.", $input->getOption('em')));
            }

            $em->getConnection()->connect($input->getOption('shard'));
        }

        foreach ($input->getOption('fixtures') as $item) {
            $this->fixturesLoader->loadFromDirectory($item);
        }

        if (!$this->fixturesLoader->getFixtures()) {
            throw new InvalidArgumentException(
                'Could not find any fixture services to load.'
            );
        }
        $purger = new ORMPurger($em);
        $purger->setPurgeMode($input->getOption('purge-with-truncate') ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($this->fixturesLoader->getFixtures(), $input->getOption('append'));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string $question
     * @param bool $default
     */
    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);

        return $questionHelper->ask($input, $output, $question);
    }
}
