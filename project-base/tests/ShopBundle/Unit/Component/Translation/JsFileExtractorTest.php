<?php

namespace Tests\ShopBundle\Unit\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Javascript\Parser\JsFunctionCallParser;
use Shopsys\FrameworkBundle\Component\Javascript\Parser\JsStringParser;
use Shopsys\FrameworkBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParserFactory;
use Shopsys\FrameworkBundle\Component\Translation\JsFileExtractor;

class JsFileExtractorTest extends TestCase
{
    public function testExtract()
    {
        $fileName = 'test.js';

        $catalogue = $this->extract(__DIR__ . '/' . $fileName);

        $expected = new MessageCatalogue();

        $message = new Message('trans test', 'messages');
        $message->addSource(new FileSource($fileName, 1));
        $expected->add($message);

        $message = new Message('transChoice test', 'messages');
        $message->addSource(new FileSource($fileName, 3));
        $expected->add($message);

        $message = new Message('trans test with domain', 'testDomain');
        $message->addSource(new FileSource($fileName, 5));
        $expected->add($message);

        $message = new Message('transChoice test with domain', 'testDomain');
        $message->addSource(new FileSource($fileName, 7));
        $expected->add($message);

        $message = new Message('concatenated message', 'messages');
        $message->addSource(new FileSource($fileName, 9));
        $expected->add($message);

        $this->assertEquals($expected, $catalogue);
    }

    private function extract($filename)
    {
        if (!is_file($filename)) {
            throw new \RuntimeException(sprintf('The file "%s" does not exist.', $filename));
        }
        $file = new \SplFileInfo($filename);

        $extractor = $this->getExtractor();

        $catalogue = new MessageCatalogue();
        $extractor->visitFile($file, $catalogue);

        return $catalogue;
    }

    private function getExtractor()
    {
        $jsFunctionCallParser = new JsFunctionCallParser();
        $jsStringParser = new JsStringParser();
        $jsTranslatorCallParserFactory = new JsTranslatorCallParserFactory(
            $jsFunctionCallParser,
            $jsStringParser
        );
        return new JsFileExtractor($jsTranslatorCallParserFactory->create());
    }
}
