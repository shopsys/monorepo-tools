<?php

namespace Shopsys\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor as OriginalTwigFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use ReflectionObject;

class TwigFileExtractor implements FileVisitorInterface
{
    /**
     * @var \JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor
     */
    private $originalTwigFileExtractor;

    /**
     * @param \JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor $originalTwigFileExtractor
     */
    public function __construct(OriginalTwigFileExtractor $originalTwigFileExtractor)
    {
        $this->originalTwigFileExtractor = $originalTwigFileExtractor;

        $this->injectCustomVisitor();
    }

    /**
     * We want to extract messages from custom Twig translation filters "transHtml" and "transchoiceHtml"
     * but original \JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor is not open for that type of extension
     * so we need to inject our \Shopsys\ShopBundle\Component\Translation\CustomTransFiltersVisitor using ReflectionObject
     */
    private function injectCustomVisitor()
    {
        $reflectionObject = new ReflectionObject($this->originalTwigFileExtractor);
        $traverserReflectionProperty = $reflectionObject->getProperty('traverser');
        $traverserReflectionProperty->setAccessible(true);
        $traverser = $traverserReflectionProperty->getValue($this->originalTwigFileExtractor);
        /** @var $traverser \Twig_NodeTraverser */
        $traverser->addVisitor(new CustomTransFiltersVisitor());
    }

    /**
     * {@inheritdoc}
     */
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue)
    {
        $this->originalTwigFileExtractor->visitFile($file, $catalogue);
    }

    /**
     * {@inheritdoc}
     */
    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->originalTwigFileExtractor->visitPhpFile($file, $catalogue, $ast);
    }

    /**
     * {@inheritdoc}
     */
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast)
    {
        $this->originalTwigFileExtractor->visitTwigFile($file, $catalogue, $ast);
    }
}
