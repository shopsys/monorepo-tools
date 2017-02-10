<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Translation;

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

    public function testTransWithNotTranslatedMessageAndSourceLocaleReturnsSourceMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            null,
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransWithTranslatedMessageAndSourceLocaleReturnsTranslatedMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('trans')
            ->with(
                $this->identicalTo('normalized source message %parameter%'),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $messageCatalogue = new MessageCatalogue(
            Translator::SOURCE_LOCALE,
            [
                'translationDomain' => ['normalized source message %parameter%' => 'translated message %parameter%'],
            ]
        );

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }

    public function testTransWithSourceLocaleAsDefaultLocaleReturnsSourceMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('getLocale')
            ->willReturn(Translator::SOURCE_LOCALE);

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value']
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransWithNotTranslatedMessageAndNonSourceLocaleReturnsSourceMessageWithHashes() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('trans')
            ->with(
                $this->identicalTo('normalized source message %parameter%'),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('source message parameter value');

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            null,
            'nonSourceLocale'
        );

        $this->assertSame('##source message parameter value', $translatedMessage);
    }

    public function testTransWithTranslatedMessageAndNonSourceLocaleReturnsTranslatedMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('trans')
            ->with(
                $this->identicalTo('normalized source message %parameter%'),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $messageCatalogue = new MessageCatalogue(
            'nonSourceLocale',
            [
                'translationDomain' => ['normalized source message %parameter%' => 'translated message %parameter%'],
            ]
        );

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('source message %parameter%'))
            ->willReturn('normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->trans(
            'source message %parameter%',
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            'nonSourceLocale'
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithNotTranslatedMessageAndSourceLocaleReturnsSourceMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            null,
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithTranslatedMessageAndSourceLocaleReturnsTranslatedMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('transChoice')
            ->with(
                $this->identicalTo('{0}zero|{1}normalized source message %parameter%'),
                $this->identicalTo(1),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $messageCatalogue = new MessageCatalogue(
            Translator::SOURCE_LOCALE,
            [
                'translationDomain' => ['{0}zero|{1}normalized source message %parameter%' => '{0}zero|{1}translated message %parameter%'],
            ]
        );

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            Translator::SOURCE_LOCALE
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithSourceLocaleAsDefaultLocaleReturnsSourceMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('getLocale')
            ->willReturn(Translator::SOURCE_LOCALE);

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value']
        );

        $this->assertSame('normalized source message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithNotTranslatedMessageAndNonSourceLocaleReturnsSourceMessageWithHashes() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('transChoice')
            ->with(
                $this->identicalTo('{0}zero|{1}normalized source message %parameter%'),
                $this->identicalTo(1),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('source message parameter value');

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn(new MessageCatalogue('nonSourceLocale', []));

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            null,
            'nonSourceLocale'
        );

        $this->assertSame('##source message parameter value', $translatedMessage);
    }

    public function testTransChoiceWithTranslatedMessageAndNonSourceLocaleReturnsTranslatedMessage() {
        $originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->getMock();

        $originalTranslatorMock->expects($this->any())->method('transChoice')
            ->with(
                $this->identicalTo('{0}zero|{1}normalized source message %parameter%'),
                $this->identicalTo(1),
                $this->identicalTo(['%parameter%' => 'parameter value'])
            )
            ->willReturn('translated message parameter value');

        $originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
            ->getMock();

        $messageCatalogue = new MessageCatalogue(
            'nonSourceLocale',
            [
                'translationDomain' => ['{0}zero|{1}normalized source message %parameter%' => '{0}zero|{1}translated message %parameter%'],
            ]
        );

        $originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
            ->willReturn($messageCatalogue);

        $identityTranslator = new IdentityTranslator(new MessageSelector());

        $messageIdNormalizerMock = $this->getMockBuilder(MessageIdNormalizer::class)
            ->getMock();

        $messageIdNormalizerMock->expects($this->atLeastOnce())->method('normalizeMessageId')
            ->with($this->identicalTo('{0}zero|{1}source message %parameter%'))
            ->willReturn('{0}zero|{1}normalized source message %parameter%');

        $translator = new Translator(
            $originalTranslatorMock,
            $originalTranslatorBagMock,
            $identityTranslator,
            $messageIdNormalizerMock
        );

        $translatedMessage = $translator->transChoice(
            '{0}zero|{1}source message %parameter%',
            1,
            ['%parameter%' => 'parameter value'],
            'translationDomain',
            'nonSourceLocale'
        );

        $this->assertSame('translated message parameter value', $translatedMessage);
    }

}
