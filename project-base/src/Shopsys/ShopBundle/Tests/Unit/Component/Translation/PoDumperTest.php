<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Translation\MessageIdNormalizer;
use Shopsys\ShopBundle\Component\Translation\PoDumper;

class PoDumperTest extends PHPUnit_Framework_TestCase {

    public function testDump() {
        $source = new FileSource('filepath', 10, 20);

        $message = new Message('message id');
        $message->setNew(false);
        $message->setMeaning('meaning');
        $message->setLocaleString('locale string');
        $message->setDesc('desc');
        $message->addSource($source);

        $catalogue = new MessageCatalogue();
        $catalogue->setLocale('en');
        $catalogue->add($message);

        $messageIdNormalizer = new MessageIdNormalizer();
        $poDumper = new PoDumper($messageIdNormalizer);

        $dump = $poDumper->dump($catalogue);
        $expectedDump = file_get_contents(__DIR__ . '/' . 'dump.po');

        $this->assertEquals($expectedDump, $dump);
    }

}
