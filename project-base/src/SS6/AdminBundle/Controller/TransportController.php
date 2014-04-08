<?php

namespace SS6\AdminBundle\Controller;

use SS6\AdminBundle\Form\Transport\TransportFormData;
use SS6\AdminBundle\Form\Transport\TransportFormType;
use SS6\CoreBundle\Model\Transport\Entity\Transport;
use SS6\CoreBundle\Model\Transport\Facade\TransportEditFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransportController extends Controller {

	public function newAction(Request $request) {
		$formData = new TransportFormData();
		$form = $this->createForm(new TransportFormType(), $formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transport = new Transport(
				$formData->getName(), 
				$formData->getPrice(), 
				$formData->getDescription(), 
				$formData->isHidden()
			);
			
			$transportEditFacade = $this->get('ss6.core.transport.transport_edit_facade');
			/* @var $transportEditFacade TransportEditFacade */
			$transportEditFacade->create($transport);
		}

		return $this->render('SS6AdminBundle:Content:Transport/detail.html.twig', array(
				'form' => $form->createView()
		));
	}

}
