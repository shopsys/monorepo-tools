<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Translation;

use ReflectionClass;
use SS6\ShopBundle\Component\Translation\Translator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageSelector;

class TranslatorTest extends \PHPUnit_Framework_TestCase {

	public function testTransWithParameters() {
		$messageSelector = new MessageSelector();
		$containerMock = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
		$catalogueSourceLocale = new MessageCatalogue(Translator::SOURCE_LOCALE);
		$catalogueOtherLocale = new MessageCatalogue('otherLocale');
		$catalogueOtherLocale->add(['Foo %%bar%%' => 'Foo %%bar%%'], Translator::DEFAULT_DOMAIN);
		$catalogues = [
			Translator::SOURCE_LOCALE => $catalogueSourceLocale,
			'otherLocale' => $catalogueOtherLocale,
		];

		$translator = new Translator($containerMock, $messageSelector);
		$reflectionClass = new ReflectionClass(Translator::class);
		$reflectionProperty = $reflectionClass->getProperty('catalogues');
		$reflectionProperty->setAccessible(true);
		$reflectionProperty->setValue($translator, $catalogues);

		$translation = $translator->trans('Foo %%bar%%', ['%%bar%%' => 'baz'], Translator::DEFAULT_DOMAIN, 'otherLocale');
		$this->assertSame('Foo baz', $translation);

		$notTranslated = $translator->trans('FooBar %%bar%%', ['%%bar%%' => 'baz'], Translator::DEFAULT_DOMAIN, 'otherLocale');
		$this->assertSame(Translator::NOT_TRANSLATED_PREFIX . 'FooBar baz', $notTranslated);

		$notTranslatedInSourceLanguage = $translator->trans(
			'FooBar %%bar%%',
			['%%bar%%' => 'baz'],
			Translator::DEFAULT_DOMAIN,
			Translator::SOURCE_LOCALE
		);
		$this->assertSame('FooBar baz', $notTranslatedInSourceLanguage);
	}

	public function testTransChoiceWithParameters() {
		$messageSelector = new MessageSelector();
		$containerMock = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
		$catalogueSourceLocale = new MessageCatalogue(Translator::SOURCE_LOCALE);
		$catalogueOtherLocale = new MessageCatalogue('otherLocale');
		$catalogueOtherLocale->add(['Foo %%bar%%' => 'Foo %%bar%%'], Translator::DEFAULT_DOMAIN);
		$catalogues = [
			Translator::SOURCE_LOCALE => $catalogueSourceLocale,
			'otherLocale' => $catalogueOtherLocale,
		];

		$translator = new Translator($containerMock, $messageSelector);
		$reflectionClass = new ReflectionClass(Translator::class);
		$reflectionProperty = $reflectionClass->getProperty('catalogues');
		$reflectionProperty->setAccessible(true);
		$reflectionProperty->setValue($translator, $catalogues);

		$translation = $translator->transChoice('Foo %%bar%%', 0, ['%%bar%%' => 'baz'], Translator::DEFAULT_DOMAIN, 'otherLocale');
		$this->assertSame('Foo baz', $translation);

		$notTranslated = $translator->transChoice('FooBar %%bar%%', 0, ['%%bar%%' => 'baz'], Translator::DEFAULT_DOMAIN, 'otherLocale');
		$this->assertSame(Translator::NOT_TRANSLATED_PREFIX . 'FooBar baz', $notTranslated);

		$notTranslatedInSourceLanguage = $translator->transChoice(
			'FooBar %%bar%%',
			0,
			['%%bar%%' => 'baz'],
			Translator::DEFAULT_DOMAIN,
			Translator::SOURCE_LOCALE
		);
		$this->assertSame('FooBar baz', $notTranslatedInSourceLanguage);
	}

	public function testTransChoice() {
		$messageSelector = new MessageSelector();
		$containerMock = $this->getMockBuilder(ContainerInterface::class)->getMockForAbstractClass();
		$catalogueSourceLocale = new MessageCatalogue(Translator::SOURCE_LOCALE);
		$catalogues = [Translator::SOURCE_LOCALE => $catalogueSourceLocale];

		$translator = new Translator($containerMock, $messageSelector);
		$reflectionClass = new ReflectionClass(Translator::class);
		$reflectionProperty = $reflectionClass->getProperty('catalogues');
		$reflectionProperty->setAccessible(true);
		$reflectionProperty->setValue($translator, $catalogues);

		$message = '{0}none|[1,5]1 to 5|[6,Inf]too much';

		$translationZero = $translator->transChoice($message, 0, [], Translator::DEFAULT_DOMAIN, Translator::SOURCE_LOCALE);
		$this->assertSame('none', $translationZero);

		$translation1to5 = $translator->transChoice($message, 5, [], Translator::DEFAULT_DOMAIN, Translator::SOURCE_LOCALE);
		$this->assertSame('1 to 5', $translation1to5);

		$translationTooMuch = $translator->transChoice($message, 6, [], Translator::DEFAULT_DOMAIN, Translator::SOURCE_LOCALE);
		$this->assertSame('too much', $translationTooMuch);
	}

}
