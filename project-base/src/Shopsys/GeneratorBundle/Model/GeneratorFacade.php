<?php

namespace Shopsys\GeneratorBundle\Model;

use Shopsys\GeneratorBundle\Model\GeneratorCollectionFactory;
use Shopsys\GeneratorBundle\Model\GeneratorsFormFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class GeneratorFacade {

    /**
     * @var \Shopsys\GeneratorBundle\Model\GeneratorCollection
     */
    private $generatorCollection;

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    public function __construct(
        KernelInterface $kernel,
        GeneratorCollectionFactory $generatorCollectionFactory
    ) {
        $this->kernel = $kernel;
        $this->generatorCollection = $generatorCollectionFactory->create();
    }

    /**
     * @param array $formData
     * @return string[]
     */
    public function generate(array $formData) {
        $bundle = $this->kernel->getBundle($formData['bundle']);
        $filepaths = [];
        foreach ($this->generatorCollection->getGenerators() as $generator) {
            $generatorEnableFormName = $generator->getName() . GeneratorsFormFactory::GENERATOR_FORM_ENABLE_POSTFIX;
            if ($formData[$generatorEnableFormName] === true && array_key_exists($generator->getName(), $formData)) {
                $filepaths[] = $generator->generate($bundle, $formData[$generator->getName()]);
            }
        }

        return $filepaths;
    }

    /**
     * @return string[]
     */
    public function getGeneratorsNames() {
        $names = [];
        foreach ($this->generatorCollection->getGenerators() as $generator) {
            $names[] = $generator->getName();
        }
        return $names;
    }

}
