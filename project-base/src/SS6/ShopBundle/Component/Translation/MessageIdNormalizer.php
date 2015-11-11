<?php

namespace SS6\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;

class MessageIdNormalizer {

	/**
	 * @param string $messageId
	 * @return string
	 */
	public function normalizeMessageId($messageId) {
		return trim(preg_replace('~\s+~u', ' ', $messageId));
	}

	/**
	 * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
	 * @return \JMS\TranslationBundle\Model\MessageCatalogue
	 */
	public function getNormalizedCatalogue(MessageCatalogue $catalogue) {
		$normalizedCatalogue = new MessageCatalogue();
		$normalizedCatalogue->setLocale($catalogue->getLocale());

		foreach ($catalogue->getDomains() as $domain => $messageCollection) {
			foreach ($messageCollection->all() as $message) {
				$normalizedMessage = $this->getNormalizedMessage($message, $domain);
				$normalizedCatalogue->add($normalizedMessage);
			}
		}

		return $normalizedCatalogue;
	}

	/**
	 * @param \JMS\TranslationBundle\Model\Message $message
	 * @param string $domain
	 * @return \JMS\TranslationBundle\Model\Message
	 */
	private function getNormalizedMessage(Message $message, $domain) {
		$normalizedMessageId = $this->normalizeMessageId($message->getId());

		$normalizedMessage = new Message($normalizedMessageId, $domain);
		$normalizedMessage->setDesc($message->getDesc());
		$normalizedMessage->setLocaleString($message->getLocaleString());
		$normalizedMessage->setMeaning($message->getMeaning());
		$normalizedMessage->setNew($message->isNew());
		foreach ($message->getSources() as $source) {
			$normalizedMessage->addSource($source);
		}

		return $normalizedMessage;
	}

}
