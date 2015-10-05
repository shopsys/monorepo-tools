<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Translation;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Translation\Translator;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorTest extends PHPUnit_Framework_TestCase {

	public function testTransWithSourceLocaleReturnsSourceMessage() {
		$originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
			->getMock();

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

		$translatedMessage = $translator->trans(
			'source message %parameter%',
			['%parameter%' => 'parameter value'],
			null,
			Translator::SOURCE_LOCALE
		);

		$this->assertSame('source message parameter value', $translatedMessage);
	}

	public function testTransWithSourceLocaleAsDefaultLocaleReturnsSourceMessage() {
		$originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
			->getMock();

		$originalTranslatorMock->expects($this->any())->method('getLocale')
			->willReturn(Translator::SOURCE_LOCALE);

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

		$translatedMessage = $translator->trans(
			'source message %parameter%',
			['%parameter%' => 'parameter value']
		);

		$this->assertSame('source message parameter value', $translatedMessage);
	}

	public function testTransWithNotTranslatedMessageAndNonSourceLocaleReturnsSourceMessageWithHashes() {
		$originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
			->getMock();

		$originalTranslatorMock->expects($this->any())->method('trans')
			->with(
				$this->identicalTo('source message %parameter%'),
				$this->identicalTo(['%parameter%' => 'parameter value'])
			)
			->willReturn('source message parameter value');

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
			->willReturn(new MessageCatalogue('nonSourceLocale', []));

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

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
				$this->identicalTo('source message %parameter%'),
				$this->identicalTo(['%parameter%' => 'parameter value'])
			)
			->willReturn('translated message parameter value');

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$messageCatalogue = new MessageCatalogue(
			'nonSourceLocale',
			[
				'translationDomain' => ['source message %parameter%' => 'translated message %parameter%'],
			]
		);

		$originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
			->willReturn($messageCatalogue);

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

		$translatedMessage = $translator->trans(
			'source message %parameter%',
			['%parameter%' => 'parameter value'],
			'translationDomain',
			'nonSourceLocale'
		);

		$this->assertSame('translated message parameter value', $translatedMessage);
	}

	public function testTransChoiceWithSourceLocaleReturnsSourceMessage() {
		$originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
			->getMock();

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

		$translatedMessage = $translator->transChoice(
			'{0}zero|{1}source message %parameter%',
			1,
			['%parameter%' => 'parameter value'],
			null,
			Translator::SOURCE_LOCALE
		);

		$this->assertSame('source message parameter value', $translatedMessage);
	}

	public function testTransChoiceWithSourceLocaleAsDefaultLocaleReturnsSourceMessage() {
		$originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
			->getMock();

		$originalTranslatorMock->expects($this->any())->method('getLocale')
			->willReturn(Translator::SOURCE_LOCALE);

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

		$translatedMessage = $translator->transChoice(
			'{0}zero|{1}source message %parameter%',
			1,
			['%parameter%' => 'parameter value']
		);

		$this->assertSame('source message parameter value', $translatedMessage);
	}

	public function testTransChoiceWithNotTranslatedMessageAndNonSourceLocaleReturnsSourceMessageWithHashes() {
		$originalTranslatorMock = $this->getMockBuilder(TranslatorInterface::class)
			->getMock();

		$originalTranslatorMock->expects($this->any())->method('transChoice')
			->with(
				$this->identicalTo('{0}zero|{1}source message %parameter%'),
				$this->identicalTo(1),
				$this->identicalTo(['%parameter%' => 'parameter value'])
			)
			->willReturn('source message parameter value');

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
			->willReturn(new MessageCatalogue('nonSourceLocale', []));

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

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
				$this->identicalTo('{0}zero|{1}source message %parameter%'),
				$this->identicalTo(1),
				$this->identicalTo(['%parameter%' => 'parameter value'])
			)
			->willReturn('translated message parameter value');

		$originalTranslatorBagMock = $this->getMockBuilder(TranslatorBagInterface::class)
			->getMock();

		$messageCatalogue = new MessageCatalogue(
			'nonSourceLocale',
			[
				'translationDomain' => ['{0}zero|{1}source message %parameter%' => '{0}zero|{1}translated message %parameter%'],
			]
		);

		$originalTranslatorBagMock->expects($this->any())->method('getCatalogue')
			->willReturn($messageCatalogue);

		$messageSelector = new MessageSelector();

		$translator = new Translator($originalTranslatorMock, $originalTranslatorBagMock, $messageSelector);

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
