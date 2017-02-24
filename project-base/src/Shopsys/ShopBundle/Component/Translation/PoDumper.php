<?php

namespace Shopsys\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Model\SourceInterface;
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

        $messages = $catalogue->getDomain($domain)->all();
        $sortedMessages = $this->sortMessagesByMessageId($messages);

        foreach ($sortedMessages as $message) {
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

        $sources = $message->getSources();
        $sortedSources = $this->sortSourcesByFixedSourcePathAndLineNumber($sources);

        foreach ($sortedSources as $source) {
            if ($source instanceof FileSource) {
                $sourcePath = $this->fixFileSourcePath($source);

                $output .= sprintf('#: %s:%s' . "\n", $this->escape($sourcePath), $this->escape($source->getLine()));
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

    /**
     * FileSource path may contain corrupted paths (especially when dumped on Windows machine):
     * - Unix and Windows directory separators may mix
     * - Path may end with a separator
     * - A path may contain unresolved .. directories
     * To fix this some level of path normalization is needed.
     *
     * Examples of method input / output:
     * - directory/file.html/ => directory/file.html
     * - directory\file.html => directory/file.html
     * - directory/secondDirectory/..\file.html => directory/file.html
     * - ../../directory///file.html => directory/file.html
     * - ./directory/./file.html => directory/file.html
     *
     * @param \JMS\TranslationBundle\Model\FileSource $source
     * @return string
     */
    private function fixFileSourcePath(FileSource $source)
    {
        $path = $source->getPath();
        $pathWithNormalizedSeparators = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $pathParts = explode('/', $pathWithNormalizedSeparators);

        $fixedPathParts = [];
        foreach ($pathParts as $pathPart) {
            if ($pathPart === '..') {
                array_pop($fixedPathParts);
            } elseif ($pathPart !== '.' && $pathPart !== '') {
                $fixedPathParts[] = $pathPart;
            }
        }

        return implode('/', $fixedPathParts);
    }

    /**
     * @param \JMS\TranslationBundle\Model\Message[] $messages
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    private function sortMessagesByMessageId(array $messages)
    {
        usort($messages, function (Message $messageA, Message $messageB) {
            return strcmp($messageA->getId(), $messageB->getId());
        });

        return $messages;
    }

    /**
     * @param \JMS\TranslationBundle\Model\SourceInterface[] $sources
     * @return \JMS\TranslationBundle\Model\SourceInterface[]
     */
    private function sortSourcesByFixedSourcePathAndLineNumber(array $sources)
    {
        usort($sources, function (SourceInterface $sourceA, SourceInterface $sourceB) {
            if ($sourceA instanceof FileSource && $sourceB instanceof FileSource) {
                $pathsComparisonResult = strcmp(
                    $this->fixFileSourcePath($sourceA),
                    $this->fixFileSourcePath($sourceB)
                );

                if ($pathsComparisonResult !== 0) {
                    return $pathsComparisonResult;
                } else {
                    return $sourceA->getLine() - $sourceB->getLine();
                }
            }

            return 0;
        });

        return $sources;
    }
}
