<?php

namespace Tests\ShopBundle\Unit\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Translation\PoDumper;

class PoDumperTest extends TestCase
{
    public function testDump()
    {
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

        $poDumper = new PoDumper();

        $dump = $poDumper->dump($catalogue);
        $expectedDump = file_get_contents(__DIR__ . '/' . 'dump.po');

        $this->assertEquals($expectedDump, $dump);
    }
}
