<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Order\PromoCode;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrentPromoCodeFacadeTest extends PHPUnit_Framework_TestCase
{

    public function testGetEnteredPromoCode() {
        $validPromoCode = new PromoCode(new PromoCodeData('validCode', 10.0));
        $sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
        $sessionMock->expects($this->atLeastOnce())->method('get')->willReturn($validPromoCode->getCode());
        $emMock = $this->getMock(EntityManager::class, [], [], '', false);
        $promoCodeRepositoryMock = $this->getMock(PromoCodeRepository::class, ['findByCode'], [], '', false);
        $promoCodeRepositoryMock->expects($this->atLeastOnce())->method('findByCode')->willReturn($validPromoCode);

        $promoCodeFacade = new PromoCodeFacade($emMock, $promoCodeRepositoryMock);
        $currentPromoCodeFacade = new CurrentPromoCodeFacade($promoCodeFacade, $sessionMock);

        $this->assertSame($validPromoCode, $currentPromoCodeFacade->getValidEnteredPromoCodeOrNull());
    }

    public function testGetEnteredPromoCodeInvalid() {
        $validPromoCode = new PromoCode(new PromoCodeData('validCode', 10.0));
        $sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
        $sessionMock->expects($this->atLeastOnce())->method('get')->willReturn($validPromoCode->getCode());
        $emMock = $this->getMock(EntityManager::class, [], [], '', false);
        $promoCodeRepositoryMock = $this->getMock(PromoCodeRepository::class, ['findByCode'], [], '', false);
        $promoCodeRepositoryMock->expects($this->atLeastOnce())->method('findByCode')->willReturn(null);

        $promoCodeFacade = new PromoCodeFacade($emMock, $promoCodeRepositoryMock);
        $currentPromoCodeFacade = new CurrentPromoCodeFacade($promoCodeFacade, $sessionMock);

        $this->assertNull($currentPromoCodeFacade->getValidEnteredPromoCodeOrNull());
    }

    public function testSetEnteredPromoCode() {
        $enteredCode = 'validCode';
        $validPromoCode = new PromoCode(new PromoCodeData('validCode', 10.0));
        $sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
        $sessionMock->expects($this->atLeastOnce())->method('set')->with(
            $this->anything(),
            $this->equalTo($enteredCode)
        );

        $emMock = $this->getMock(EntityManager::class, [], [], '', false);
        $promoCodeRepositoryMock = $this->getMock(PromoCodeRepository::class, ['findByCode'], [], '', false);
        $promoCodeRepositoryMock->expects($this->atLeastOnce())->method('findByCode')->willReturn($validPromoCode);

        $promoCodeFacade = new PromoCodeFacade($emMock, $promoCodeRepositoryMock);
        $currentPromoCodeFacade = new CurrentPromoCodeFacade($promoCodeFacade, $sessionMock);
        $currentPromoCodeFacade->setEnteredPromoCode($enteredCode);
    }

    public function testSetEnteredPromoCodeInvalid() {
        $enteredCode = 'invalidCode';
        $sessionMock = $this->getMockForAbstractClass(SessionInterface::class, ['get']);
        $sessionMock->expects($this->never())->method('set');

        $emMock = $this->getMock(EntityManager::class, [], [], '', false);
        $promoCodeRepositoryMock = $this->getMock(PromoCodeRepository::class, ['findByCode'], [], '', false);
        $promoCodeRepositoryMock->expects($this->atLeastOnce())->method('findByCode')->willReturn(null);

        $promoCodeFacade = new PromoCodeFacade($emMock, $promoCodeRepositoryMock);
        $currentPromoCodeFacade = new CurrentPromoCodeFacade($promoCodeFacade, $sessionMock);
        $this->setExpectedException(\Shopsys\ShopBundle\Model\Order\PromoCode\Exception\InvalidPromoCodeException::class);
        $currentPromoCodeFacade->setEnteredPromoCode($enteredCode);
    }

}
