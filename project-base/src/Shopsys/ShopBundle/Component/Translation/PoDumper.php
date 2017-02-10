<?php

namespace Shopsys\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Dumper\DumperInterface;

class PoDumper implements DumperInterface
{
    /**
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param string $domain
     * @return string
     */
    public function dump(MessageCatalogue $catalogue, $domain = 'messages')
    {
        $output = 'msgid ""' . "\n";
        $output .= 'msgstr ""' . "\n";
        $output .= '"Content-Type: text/plain; charset=UTF-8\n"' . "\n";
        $output .= '"Content-Transfer-Encoding: 8bit\n"' . "\n";
        $output .= '"Language: ' . $catalogue->getLocale() . '\n"' . "\n";
        $output .= "\n";

        foreach ($catalogue->getDomain($domain)->all() as $message) {
            /* @var $message \JMS\TranslationBundle\Model\Message */
            $output .= $this->getReferences($message);
            $output .= sprintf('msgid "%s"' . "\n", $this->escape($message->getId()));
            if ($message->isNew()) {
                $output .= 'msgstr ""' . "\n";
            } else {
                $output .= sprintf('msgstr "%s"' . "\n", $this->escape($message->getLocaleString()));
            }

            $output .= "\n";
        }

        return $output;
    }

    /**
     * @param \JMS\TranslationBundle\Model\Message $message
     * @return string
     */
    private function getReferences(Message $message)
    {
        $output = '';

        foreach ($message->getSources() as $source) {
            /* var $source \JMS\TranslationBundle\Model\SourceInterface */
            if ($source instanceof FileSource) {
                $output .= sprintf('#: %s:%s' . "\n", $this->escape($source->getPath()), $this->escape($source->getLine()));
            }
        }

        return $output;
    }

    /**
     * @param string $str
     * @return string
     */
    private function escape($str)
    {
        return addcslashes($str, "\0..\37\42\134");
    }
}
