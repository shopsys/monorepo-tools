<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Form\Front\Newsletter\SubscriptionFormType;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends FrontBaseController {

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function subscribeEmailAction(Request $request) {
		$form = $this->createSubscriptionForm();
		$form->handleRequest($request);

		if ($form->isValid()) {
			$email = $form->getData()['email'];

			return $this->render('@SS6Shop/Front/Inline/Newsletter/send.html.twig');
		}

		return $this->render('@SS6Shop/Front/Inline/Newsletter/subscription.html.twig', [
			'form' => $form->createView(),
		]);
	}

	public function subscriptionAction() {
		$form = $this->createSubscriptionForm();

		return $this->render('@SS6Shop/Front/Inline/Newsletter/subscription.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @return \Symfony\Component\Form\Form
	 */
	private function createSubscriptionForm() {
		$formOptions = ['action' => $this->generateUrl('front_newsletter_send')];
		return $this->createForm(new SubscriptionFormType(), null, $formOptions);
	}

}
