<?php

namespace Shopsys\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PLUG\JavaScript\JParser;
use PLUG\JavaScript\JTokenizer;
use Shopsys\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser;
use SplFileInfo;
use Twig_Node;

class JsFileExtractor implements FileVisitorInterface
{
    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @var \JMS\TranslationBundle\Model\MessageCatalogue
     */
    private $catalogue;

    /**
     * @var \Shopsys\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser
     */
    private $jsTranslatorCallParser;

    public function __construct(JsTranslatorCallParser $jsTranslatorCallParser)
    {
        $this->jsTranslatorCallParser = $jsTranslatorCallParser;
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     */
    public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue)
    {
        if ('.js' !== substr($file, -3)) {
            return;
        }

        $this->file = $file;
        $this->catalogue = $catalogue;

        $filename = $file->getRealPath();
        $contents = file_get_contents($filename);

        try {
            $this->parseFile($contents);
        } catch (\PLUG\parsing\ParseError $ex) {
            throw new \Shopsys\ShopBundle\Component\Translation\Exception\ExtractionException(
                $ex->getMessage() . "\n" . 'in file ' . $this->file->getRealPath()
            );
        } catch (\Shopsys\ShopBundle\Component\Javascript\Parser\Exception\JsParserException $ex) {
            throw new \Shopsys\ShopBundle\Component\Translation\Exception\ExtractionException(
                $ex->getMessage() . ' in file ' . $this->file->getRealPath()
            );
        }
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param array $ast
     */
    public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param \Twig_Node $node
     */
    public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, Twig_Node $node)
    {
    }

    /**
     * @param string $contents
     */
    private function parseFile($contents)
    {
        $node = JParser::parse_string($contents, true, JParser::class, JTokenizer::class);

        $jsTranslatorCalls = $this->jsTranslatorCallParser->parse($node);

        foreach ($jsTranslatorCalls as $jsTranslatorCall) {
            $message = new Message($jsTranslatorCall->getMessageId(), $jsTranslatorCall->getDomain());
            $message->addSource(new FileSource(
                $this->file->getFilename(),
                $jsTranslatorCall->getCallExprNode()->get_line_num()
            ));

            $this->catalogue->add($message);
        }
    }
}
