<?php

namespace Shopsys\ShopBundle\Model\Mail;

use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;

class MailerService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MessageData $messageData
     */
    public function send(MessageData $messageData)
    {
        $message = $this->getMessageWithReplacedVariables($messageData);
        $failedRecipients = [];
        $successSend = $this->mailer->send($message, $failedRecipients);
        if (!$successSend && count($failedRecipients) > 0) {
            throw new \Shopsys\ShopBundle\Model\Mail\Exception\SendMailFailedException($failedRecipients);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MessageData $messageData
     * @return \Swift_Message
     */
    private function getMessageWithReplacedVariables(MessageData $messageData)
    {
        $toEmail = $messageData->toEmail;
        $body = $this->replaceVariables(
            $messageData->body,
            $messageData->variablesReplacementsForBody
        );
        $subject = $this->replaceVariables(
            $messageData->subject,
            $messageData->variablesReplacementsForSubject
        );
        $fromEmail = $messageData->fromEmail;
        $fromName = $messageData->fromName;

        $message = Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom($fromEmail, $fromName);
        $message->setTo($toEmail);
        if ($messageData->bccEmail !== null) {
            $message->addBcc($messageData->bccEmail);
        }
        if ($messageData->replyTo !== null) {
            $message->addReplyTo($messageData->replyTo);
        }
        $message->setContentType('text/plain; charset=UTF-8');
        $message->setBody(strip_tags($body), 'text/plain');
        $message->addPart($body, 'text/html');
        foreach ($messageData->attachmentsFilepaths as $attachmentFilepath) {
            $message->attach(Swift_Attachment::fromPath($attachmentFilepath));
        }

        return $message;
    }

    /**
     * @param string $string
     * @param array $variablesKeysAndValues
     * @return string
     */
    private function replaceVariables($string, $variablesKeysAndValues)
    {
        return strtr($string, $variablesKeysAndValues);
    }
}
