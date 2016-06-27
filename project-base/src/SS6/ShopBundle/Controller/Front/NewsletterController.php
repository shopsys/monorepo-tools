<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Form\Front\Newsletter\SubscriptionFormType;

class NewsletterController extends FrontBaseController {

	public function subscriptionAction() {
		$form = $this->createForm(new SubscriptionFormType());

		return $this->render('@SS6Shop/Front/Inline/Newsletter/subscription.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
