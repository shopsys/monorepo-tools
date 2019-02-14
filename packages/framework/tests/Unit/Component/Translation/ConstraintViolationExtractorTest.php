<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Translation\ConstraintViolationExtractor;
use SplFileInfo;

class ConstraintViolationExtractorTest extends TestCase
{
    public function testMessagesAreExtractedFromConstraintViolationClass(): void
    {
        $file = new SplFileInfo(__DIR__ . '/Resources/ConstraintViolationClass.php');

        $actualCatalogue = $this->extract($file);

        $expectedCatalogue = new MessageCatalogue();

        $message = new Message('This message will be extracted into "validators" translation domain', 'validators');
        $message->addSource(new FileSource($file->getFilename(), 16));
        $expectedCatalogue->add($message);

        $this->assertEquals($expectedCatalogue, $actualCatalogue);
    }

    public function testNothingIsExtractedFromNonConstraintClass(): void
    {
        $file = new SplFileInfo(__DIR__ . '/Resources/NonConstraintClass.php');

        $actualCatalogue = $this->extract($file);

        $expectedCatalogue = new MessageCatalogue();

        $this->assertEquals($expectedCatalogue, $actualCatalogue);
    }

    /**
     * @param \SplFileInfo $file
     * @return \JMS\TranslationBundle\Model\MessageCatalogue
     */
    private function extract(SplFileInfo $file): MessageCatalogue
    {
        $extractor = new ConstraintViolationExtractor();

        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
        $ast = $parser->parse(file_get_contents($file->getPathname()));

        $catalogue = new MessageCatalogue();
        $extractor->visitPhpFile($file, $catalogue, $ast);

        return $catalogue;
    }
}
