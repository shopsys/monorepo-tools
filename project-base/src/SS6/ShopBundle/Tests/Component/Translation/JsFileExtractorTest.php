<?php

namespace SS6\ShopBundle\Tests\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use SS6\ShopBundle\Component\Translation\JsFileExtractor;
use SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\JsTranslatorCallParserFactory;

class JsFileExtractorTest extends \PHPUnit_Framework_TestCase {

	public function testExtract() {
		$fileName = 'test.js';

		$catalogue = $this->extract(__DIR__ . '/' . $fileName);

		$expected = new MessageCatalogue();

		$message = new Message('trans test', 'messages');
		$message->addSource(new FileSource($fileName, 1, 1));
		$expected->add($message);

		$message = new Message('transChoice test', 'messages');
		$message->addSource(new FileSource($fileName, 3, 1));
		$expected->add($message);

		$message = new Message('trans test with domain', 'testDomain');
		$message->addSource(new FileSource($fileName, 5, 1));
		$expected->add($message);

		$message = new Message('transChoice test with domain', 'testDomain');
		$message->addSource(new FileSource($fileName, 7, 1));
		$expected->add($message);

		$message = new Message('concatenated message', 'messages');
		$message->addSource(new FileSource($fileName, 9, 1));
		$expected->add($message);

		$this->assertEquals($expected, $catalogue);
	}

	private function extract($filename) {
		if (!is_file($filename)) {
			throw new \RuntimeException(sprintf('The file "%s" does not exist.', $filename));
		}
		$file = new \SplFileInfo($filename);

		$extractor = $this->getExtractor();

		$catalogue = new MessageCatalogue();
		$extractor->visitFile($file, $catalogue);

		return $catalogue;
	}

	private function getExtractor() {
		$jsTranslatorCallParserFactory = new JsTranslatorCallParserFactory();
		return new JsFileExtractor($jsTranslatorCallParserFactory->create());
	}

}
