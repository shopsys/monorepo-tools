<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Order\PromoCode;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PromoCodeFacadeTest extends PHPUnit_Framework_TestCase {

	public function testGetEnteredPromoCode() {
		$validCode = 'validCode';
		$sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
		$sessionMock->expects($this->atLeastOnce())->method('get')->willReturn($validCode);
		$settingMock = $this->getMock(Setting::class, ['get'], [], '', false);
		$settingMock->expects($this->atLeastOnce())->method('get')->willReturnMap([
			[
				PromoCodeFacade::PROMO_CODE_PERCENT_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				10.0,
			],
			[
				PromoCodeFacade::PROMO_CODE_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				$validCode,
			],
		]);

		$promoCodeFacade = new PromoCodeFacade($sessionMock, $settingMock);
		$this->assertSame($validCode, $promoCodeFacade->getEnteredPromoCode());
		$this->assertSame(10.0, $promoCodeFacade->getEnteredPromoCodePercent());
	}

	public function testGetEnteredPromoCodeIvalid() {
		$validCode = 'validCode';
		$enteredCode = 'enteredCode';
		$sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
		$sessionMock->expects($this->atLeastOnce())->method('get')->willReturn($enteredCode);
		$settingMock = $this->getMock(Setting::class, ['get'], [], '', false);
		$settingMock->expects($this->atLeastOnce())->method('get')->willReturnMap([
			[
				PromoCodeFacade::PROMO_CODE_PERCENT_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				10.0,
			],
			[
				PromoCodeFacade::PROMO_CODE_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				$validCode,
			],
		]);

		$promoCodeFacade = new PromoCodeFacade($sessionMock, $settingMock);
		$this->assertNull($promoCodeFacade->getEnteredPromoCode());
		$this->assertNull($promoCodeFacade->getEnteredPromoCodePercent());
	}

	public function testSetEnteredPromoCode() {
		$validCode = 'validCode';
		$enteredCode = 'validCode';
		$sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
		$sessionMock->expects($this->atLeastOnce())->method('set')->with(
			$this->anything(),
			$this->equalTo($enteredCode)
		);
		$settingMock = $this->getMock(Setting::class, ['get'], [], '', false);
		$settingMock->expects($this->atLeastOnce())->method('get')->willReturnMap([
			[
				PromoCodeFacade::PROMO_CODE_PERCENT_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				10.0,
			],
			[
				PromoCodeFacade::PROMO_CODE_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				$validCode,
			],
		]);

		$promoCodeFacade = new PromoCodeFacade($sessionMock, $settingMock);
		$promoCodeFacade->setEnteredPromoCode($enteredCode);
	}

	public function testSetEnteredPromoCodeInvalid() {
		$validCode = 'validCode';
		$enteredCode = 'invalidCode';
		$sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
		$sessionMock->expects($this->never())->method('set');
		$settingMock = $this->getMock(Setting::class, ['get'], [], '', false);
		$settingMock->expects($this->atLeastOnce())->method('get')->willReturnMap([
			[
				PromoCodeFacade::PROMO_CODE_PERCENT_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				10.0,
			],
			[
				PromoCodeFacade::PROMO_CODE_SETTING_KEY,
				SettingValue::DOMAIN_ID_COMMON,
				$validCode,
			],
		]);

		$promoCodeFacade = new PromoCodeFacade($sessionMock, $settingMock);
		$this->setExpectedException(\SS6\ShopBundle\Model\Order\PromoCode\Exception\InvalidPromoCodeException::class);
		$promoCodeFacade->setEnteredPromoCode($enteredCode);
	}

}
