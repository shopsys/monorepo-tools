<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;

class VatFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatRepository
     */
    private $vatRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatService
     */
    private $vatService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatRepository $vatRepository
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatService $vatService
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     */
    public function __construct(
        EntityManagerInterface $em,
        VatRepository $vatRepository,
        VatService $vatService,
        Setting $setting,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
    ) {
        $this->em = $em;
        $this->vatRepository = $vatRepository;
        $this->vatService = $vatService;
        $this->setting = $setting;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
    }

    /**
     * @param int $vatId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getById($vatId)
    {
        return $this->vatRepository->getById($vatId);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat[]
     */
    public function getAll()
    {
        return $this->vatRepository->getAll();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat[]
     */
    public function getAllIncludingMarkedForDeletion()
    {
        return $this->vatRepository->getAllIncludingMarkedForDeletion();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData $vatData
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function create(VatData $vatData)
    {
        $vat = $this->vatService->create($vatData);
        $this->em->persist($vat);
        $this->em->flush();

        return $vat;
    }

    /**
     * @param int $vatId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData $vatData
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function edit($vatId, VatData $vatData)
    {
        $vat = $this->vatRepository->getById($vatId);
        $this->vatService->edit($vat, $vatData);
        $this->em->flush();

        $this->productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();

        return $vat;
    }

    /**
     * @param int $vatId
     * @param int|null $newVatId
     */
    public function deleteById($vatId, $newVatId = null)
    {
        $oldVat = $this->vatRepository->getById($vatId);
        $newVat = $newVatId ? $this->vatRepository->getById($newVatId) : null;

        if ($oldVat->isMarkedAsDeleted()) {
            throw new \Shopsys\FrameworkBundle\Model\Pricing\Vat\Exception\VatMarkedAsDeletedDeleteException();
        }

        if ($this->vatRepository->existsVatToBeReplacedWith($oldVat)) {
            throw new \Shopsys\FrameworkBundle\Model\Pricing\Vat\Exception\VatWithReplacedDeleteException();
        }

        if ($newVat !== null) {
            $newDefaultVat = $this->vatService->getNewDefaultVat(
                $this->getDefaultVat(),
                $oldVat,
                $newVat
            );
            $this->setDefaultVat($newDefaultVat);

            $this->vatRepository->replaceVat($oldVat, $newVat);
            $oldVat->markForDeletion($newVat);
        } else {
            $this->em->remove($oldVat);
        }

        $this->em->flush();
    }

    /**
     * @return int
     */
    public function deleteAllReplacedVats()
    {
        $vatsForDelete = $this->vatRepository->getVatsWithoutProductsMarkedForDeletion();
        foreach ($vatsForDelete as $vatForDelete) {
            $this->em->remove($vatForDelete);
        }
        $this->em->flush($vatsForDelete);

        return count($vatsForDelete);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getDefaultVat()
    {
        $defaultVatId = $this->setting->get(Vat::SETTING_DEFAULT_VAT);

        return $this->vatRepository->getById($defaultVatId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    public function setDefaultVat(Vat $vat)
    {
        $this->setting->set(Vat::SETTING_DEFAULT_VAT, $vat->getId());
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return bool
     */
    public function isVatUsed(Vat $vat)
    {
        $defaultVat = $this->getDefaultVat();

        return $defaultVat === $vat || $this->vatRepository->isVatUsed($vat);
    }

    /**
     * @param int $vatId
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat[]
     */
    public function getAllExceptId($vatId)
    {
        return $this->vatRepository->getAllExceptId($vatId);
    }
}
