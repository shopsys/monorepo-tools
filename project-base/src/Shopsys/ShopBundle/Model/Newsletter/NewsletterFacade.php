<?php

namespace Shopsys\ShopBundle\Model\Newsletter;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterRepository;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterSubscriber;

class NewsletterFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Shopsys\ShopBundle\Model\Newsletter\NewsletterRepository
	 */
	private $newsletterRepository;

	public function __construct(
		EntityManager $em,
		NewsletterRepository $newsletterRepository
	) {
		$this->em = $em;
		$this->newsletterRepository = $newsletterRepository;
	}

	/**
	 * @param string $email
	 */
	public function addSubscribedEmail($email) {
		if (!$this->newsletterRepository->existsSubscribedEmail($email)) {
			$newsletterSubscriber = new NewsletterSubscriber($email);
			$this->em->persist($newsletterSubscriber);
			$this->em->flush($newsletterSubscriber);
		}
	}

	/**
	 * @return \Doctrine\ORM\Internal\Hydration\IterableResult|string[][0]['email']
	 */
	public function getAllEmailsDataIterator() {
		return $this->newsletterRepository->getAllEmailsDataIterator();
	}

}
