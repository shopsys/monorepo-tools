<?php

namespace Tests\ShopBundle\Unit\Component\Translation;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Translation\MessageIdNormalizer;
use Shopsys\ShopBundle\Component\Translation\Translator;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Translation\TranslatorInterface
     */
    private $originalTranslatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Translation\TranslatorBagInterface
     */
    private $originalTranslatorBagMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Shopsys\ShopBundle\Component\Translation\MessageIdNormalizer
     */
    private $messageIdNormalizerMock;

    /**
     * @var \Symfony\Component\Translation\IdentityTranslator
     */
    private $identityTranslator;

    /**
     * @var \Shopsys\ShopBundle\Component\Translation\Translator
     */
    private $translator;

    protected function setUp()
    {
        $this->originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMock();
        $this->originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)->getMock();
        $this->messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)->getMock();
        $this->identityTranslator = new IdentityTranslator(new MessageSelector());
    }

    private function initTranslator()
    {
        $this->translator = new Translator(
            $this->originalTranslatorMock,
            $this->originalTranslatorBagMock,
            $this->identityTranslator,
            $this->messageIdNormalizerMock
        );
    }

    public function testTransWithNotTranslatedMessageAndSourceLocaleReturnsSourceMessage()
    {
        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue(Translator::SOURCE_LOCALE, []));

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            null,
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransWithTranslatedMessageAndSourceLocaleReturnsTranslatedMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('trans')
            ->with(
                $this->identicalTo('normalized source message %parameter%'),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $messageCatalogue = new MessageCatalogue(
            Translator::SOURCE_LOCALE,
            [
                'translationDomain' => ['normalized source message %parameter%' => 'translated message %parameter%'],
            ]
        );

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }

    public function testTransWithSourceLocaleAsDefaultLocaleReturnsSourceMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('getLocale')
            ->willReturn(Translator::SOURCE_LOCALE);

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue(Translator::SOURCE_LOCALE, []));

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value']
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransWithNotTranslatedMessageAndNonSourceLocaleReturnsSourceMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('trans')
            ->with(
                $this->identicalTo('normalized source message %parameter%'),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('source message parameter value');

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            null,
            'nonSourceLocale'
        );

        $this->assertSame('source message parameter value', $translatedMessage);
    }

    public function testTransWithTranslatedMessageAndNonSourceLocaleReturnsTranslatedMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('trans')
            ->with(
                $this->identicalTo('normalized source message %parameter%'),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $messageCatalogue = new MessageCatalogue(
            'nonSourceLocale',
            [
                'translationDomain' => ['normalized source message %parameter%' => 'translated message %parameter%'],
            ]
        );

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            'nonSourceLocale'
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithNotTranslatedMessageAndSourceLocaleReturnsSourceMessage()
    {
        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            null,
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithTranslatedMessageAndSourceLocaleReturnsTranslatedMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('transChoice')
            ->with(
                $this->identicalTo('{0}zero|{1}normalized source message %parameter%'),
                $this->identicalTo(1),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $messageCatalogue = new MessageCatalogue(
            Translator::SOURCE_LOCALE,
            [
                'translationDomain' => ['{0}zero|{1}normalized source message %parameter%' => '{0}zero|{1}translated message %parameter%'],
            ]
        );

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithSourceLocaleAsDefaultLocaleReturnsSourceMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('getLocale')
            ->willReturn(Translator::SOURCE_LOCALE);

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue(Translator::SOURCE_LOCALE, []));

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value']
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithNotTranslatedMessageAndNonSourceLocaleReturnsSourceMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('transChoice')
            ->with(
                $this->identicalTo('{0}zero|{1}normalized source message %parameter%'),
                $this->identicalTo(1),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('source message parameter value');

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            null,
            'nonSourceLocale'
        );

        $this->assertSame('source message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithTranslatedMessageAndNonSourceLocaleReturnsTranslatedMessage()
    {
        $this->originalTranslatorMock->expects($this->any())->method('transChoice')
            ->with(
                $this->identicalTo('{0}zero|{1}normalized source message %parameter%'),
                $this->identicalTo(1),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $messageCatalogue = new MessageCatalogue(
            'nonSourceLocale',
            [
                'translationDomain' => ['{0}zero|{1}normalized source message %parameter%' => '{0}zero|{1}translated message %parameter%'],
            ]
        );

        $this->originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $this->messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $this->initTranslator();

        $translatedMessage = $this->translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            'nonSourceLocale'
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }
}
