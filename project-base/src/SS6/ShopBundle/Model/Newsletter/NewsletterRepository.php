<?php

namespace SS6\ShopBundle\Model\Newsletter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Newsletter\NewsletterSubscriber;

class NewsletterRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getNewsletterSubscriberRepository() {
		return $this->em->getRepository(NewsletterSubscriber::class);
	}

	/**
	 * @param string $email
	 * @return bool
	 */
	public function existsSubscribedEmail($email) {
		return $this->getNewsletterSubscriberRepository()->find($email) !== null;
	}

}
