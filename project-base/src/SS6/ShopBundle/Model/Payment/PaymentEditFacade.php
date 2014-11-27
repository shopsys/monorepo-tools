<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Payment\PaymentDomain;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Payment\PaymentVisibilityCalculation;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class PaymentEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	/**
	 * @var SS6\ShopBundle\Model\Payment\PaymentVisibilityCalculation
	 */
	private $paymentVisibilityCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		EntityManager $em,
		PaymentRepository $paymentRepository,
		TransportRepository $transportRepository,
		PaymentVisibilityCalculation $paymentVisibilityCalculation,
		Domain $domain,
		ImageFacade	$imageFacade
	) {
		$this->em = $em;
		$this->paymentRepository = $paymentRepository;
		$this->transportRepository = $transportRepository;
		$this->paymentVisibilityCalculation = $paymentVisibilityCalculation;
		$this->domain = $domain;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function create(PaymentData $paymentData) {
		$payment = new Payment($paymentData);
		$this->em->persist($payment);
		$this->em->beginTransaction();
		$this->em->flush();
		$this->createPaymentDomains($payment, $paymentData->getDomains());
		$this->setAddionalDataAndFlush($payment, $paymentData);
		$this->em->commit();

		return $payment;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 */
	public function edit(Payment $payment, PaymentData $paymentData) {
		$payment->edit($paymentData);
		$this->em->beginTransaction();
		$this->deletePaymentDomainsByPayment($payment);
		$this->createPaymentDomains($payment, $paymentData->getDomains());
		$this->setAddionalDataAndFlush($payment, $paymentData);
		$this->em->commit();
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getById($id) {
		return $this->paymentRepository->getById($id);
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getByIdWithTransports($id) {
		return $this->paymentRepository->getByIdWithTransports($id);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Payment\PaymentDomain[]
	 */
	public function getPaymentDomainsByPayment(Payment $payment) {
		return $this->paymentRepository->getPaymentDomainsByPayment($payment);
	}

	/**
	 * @param int $id
	 */
	public function deleteById($id) {
		$payment = $this->getById($id);
		$payment->markAsDeleted();
		$this->em->beginTransaction();
		$this->deletePaymentDomainsByPayment($payment);
		$this->em->flush();
		$this->em->commit();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 */
	private function setAddionalDataAndFlush(Payment $payment, PaymentData $paymentData) {
		$transports = $this->transportRepository->findAllByIds($paymentData->getTransports());
		$payment->setTransports($transports);
		$this->imageFacade->uploadImage($payment, $paymentData->getImage(), null);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	public function getVisibleOnCurrentDomain() {
		$allPayments = $this->paymentRepository->findAllWithTransports();

		return $this->paymentVisibilityCalculation->filterVisible($allPayments, $this->domain->getId());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $oldVat
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $newVat
	 */
	public function replaceOldVatWithNewVat(Vat $oldVat, Vat $newVat) {
		$payments = $this->paymentRepository->getAllIncludingDeletedByVat($oldVat);
		foreach ($payments as $payment) {
			$payment->changeVat($newVat);
		}
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param array $domainIds
	 */
	private function createPaymentDomains(Payment $payment, array $domainIds) {
		foreach ($domainIds as $domainId) {
			$paymentDomain = new PaymentDomain($payment, $domainId);
			$this->em->persist($paymentDomain);
		}
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	private function deletePaymentDomainsByPayment(Payment $payment) {
		$paymentDomains = $this->getPaymentDomainsByPayment($payment);
		foreach ($paymentDomains as $paymentDomain) {
			$this->em->remove($paymentDomain);
		}
		$this->em->flush();
	}

}
