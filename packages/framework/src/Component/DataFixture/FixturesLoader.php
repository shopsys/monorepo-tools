<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture;

use Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;

/**
 * The class is copy-pasted from the final class SymfonyFixturesLoader and the method loadFromDirectory() is added to enable
 * loading data fixtures from specified directory in our custom LoadDataFixturesCommand
 * @see \Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader
 * @see \Shopsys\FrameworkBundle\Command\LoadDataFixturesCommand
 */
class FixturesLoader extends Loader
{
    /**
     * @var \Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader
     */
    private $symfonyFixturesLoader;

    /**
     * @var \Doctrine\Common\DataFixtures\FixtureInterface[]
     */
    private $loadedFixtures = [];

    public function __construct(SymfonyFixturesLoader $symfonyFixturesLoader)
    {
        $this->symfonyFixturesLoader = $symfonyFixturesLoader;
    }

    /**
     * @param \Doctrine\Common\DataFixtures\FixtureInterface[] $fixtures
     */
    public function addFixtures(array $fixtures)
    {
        // Store all loaded fixtures so that we can resolve the dependencies correctly.
        foreach ($fixtures as $fixture) {
            $this->loadedFixtures[get_class($fixture)] = $fixture;
        }

        // Now load all the fixtures
        foreach ($this->loadedFixtures as $fixture) {
            $this->addFixture($fixture);
        }
    }

    /**
     * @param \Doctrine\Common\DataFixtures\FixtureInterface $fixture
     */
    public function addFixture(FixtureInterface $fixture)
    {
        $class = get_class($fixture);
        if (!isset($this->loadedFixtures[$class])) {
            $this->loadedFixtures[$class] = $fixture;
        }

        /**
         * see https://github.com/doctrine/data-fixtures/pull/274
         * if you do not have this version of DoctrineFixturesBundle
         */
        if (!method_exists(Loader::class, 'createFixture')) {
            $this->checkForNonInstantiableFixtures($fixture);
        }

        parent::addFixture($fixture);
    }

    /**
     * @param string $class
     * @return \Doctrine\Common\DataFixtures\FixtureInterface
     */
    protected function createFixture($class)
    {
        /*
         * We don't actually need to create the fixture. We just
         * return the one that already exists.
         */

        if (!isset($this->loadedFixtures[$class])) {
            throw new \LogicException(sprintf(
                'The "%s" fixture class is trying to be loaded, but is not available. Make sure this class is defined as a service and tagged with "%s".',
                $class,
                FixturesCompilerPass::FIXTURE_TAG
            ));
        }

        return $this->loadedFixtures[$class];
    }

    /**
     * For doctrine/data-fixtures 1.2 or lower, this detects an unsupported
     * feature with DependentFixtureInterface so that we can throw a
     * clear exception.
     *
     * @param \Doctrine\Common\DataFixtures\FixtureInterface $fixture
     */
    private function checkForNonInstantiableFixtures(FixtureInterface $fixture)
    {
        if (!$fixture instanceof DependentFixtureInterface) {
            return;
        }

        foreach ($fixture->getDependencies() as $dependency) {
            if (!class_exists($dependency)) {
                continue;
            }

            if (!method_exists($dependency, '__construct')) {
                continue;
            }

            $reflectionMethod = new \ReflectionMethod($dependency, '__construct');
            foreach ($reflectionMethod->getParameters() as $param) {
                if (!$param->isOptional()) {
                    throw new \LogicException(sprintf('The getDependencies() method returned a class (%s) that has required constructor arguments. Upgrade to "doctrine/data-fixtures" version 1.3 or higher to support this.', $dependency));
                }
            }
        }
    }

    /**
     * @param string $dir
     */
    public function loadFromDirectory($dir)
    {
        $fixtures = $this->symfonyFixturesLoader->loadFromDirectory($dir);
        $this->addFixtures($fixtures);
    }
}
