<?php

namespace Shopsys\GeneratorBundle\Model;

use Shopsys\GeneratorBundle\Model\Generator\RepositoryGenerator;
use Shopsys\GeneratorBundle\Model\GeneratorCollection;

class GeneratorCollectionFactory {

    /**
     * @var \Shopsys\GeneratorBundle\Model\Generator\RepositoryGenerator
     */
    private $repositoryGenerator;

    /**
     * @var string[]
     */
    private $skeletonDirs;

    public function __construct(array $skeletonDirs, RepositoryGenerator $repositoryGenerator) {
        $this->skeletonDirs = $skeletonDirs;
        $this->repositoryGenerator = $repositoryGenerator;
    }

    /**
     * @return \Shopsys\GeneratorBundle\Model\GeneratorCollection
     */
    public function create() {
        $generatorCollection = new GeneratorCollection($this->skeletonDirs);
        $generatorCollection->addGenerator($this->repositoryGenerator);

        return $generatorCollection;
    }

}
