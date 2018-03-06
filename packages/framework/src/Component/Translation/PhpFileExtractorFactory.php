<?php

namespace Shopsys\FrameworkBundle\Component\Translation;

use Doctrine\Common\Annotations\DocParser;

class PhpFileExtractorFactory
{
    /**
     * @var \Doctrine\Common\Annotations\DocParser
     */
    private $docParser;

    public function __construct(DocParser $docParser)
    {
        $this->docParser = $docParser;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Translation\PhpFileExtractor
     */
    public function create()
    {
        $transMethodSpecifications = [
            new TransMethodSpecification('trans', 0, 2),
            new TransMethodSpecification('transChoice', 0, 3),
            new TransMethodSpecification('t', 0, 2),
            new TransMethodSpecification('tc', 0, 3),
        ];

        return new PhpFileExtractor($this->docParser, $transMethodSpecifications);
    }
}
