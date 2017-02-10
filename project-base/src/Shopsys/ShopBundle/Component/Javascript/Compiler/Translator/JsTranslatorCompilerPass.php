<?php

namespace Shopsys\ShopBundle\Component\Javascript\Compiler\Translator;

use PLUG\JavaScript\JNodes\nonterminal\JProgramNode;
use Shopsys\ShopBundle\Component\Javascript\Compiler\JsCompilerPassInterface;
use Shopsys\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser;
use Shopsys\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParserFactory;
use Shopsys\ShopBundle\Component\Translation\Translator;

class JsTranslatorCompilerPass implements JsCompilerPassInterface
{
    /**
     * @var \Shopsys\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser
     */
    private $jsTranslatorCallParser;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(
        JsTranslatorCallParser $jsTranslatorCallParser,
        Translator $translator
    ) {
        $this->jsTranslatorCallParser = $jsTranslatorCallParser;
        $this->translator = $translator;
    }

    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JProgramNode $node
     */
    public function process(JProgramNode $node)
    {
        $jsTranslatorsCalls = $this->jsTranslatorCallParser->parse($node);

        foreach ($jsTranslatorsCalls as $jsTranslatorsCall) {
            $messageIdArgumentNode = $jsTranslatorsCall->getMessageIdArgumentNode();

            // It is necessary to mark each part of pluralization with two hashes when the message is not translated.
            // Therefore custom method is used instead of using $this->translaor->trans method for transChoice calls.
            if ($jsTranslatorsCall->getFunctionName() === JsTranslatorCallParserFactory::METHOD_NAME_TRANS_CHOICE) {
                $translatedMessage = $this->translate($jsTranslatorsCall);
            } else {
                $translatedMessage = $this->translator->trans($jsTranslatorsCall->getMessageId());
            }

            $messageIdArgumentNode->terminate(json_encode($translatedMessage));
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCall $jsTranslatorsCall
     * @return string
     */
    private function translate($jsTranslatorsCall)
    {
        $locale = $this->translator->getLocale();
        $catalogue = $this->translator->getCatalogue($locale);
        $messageId = $jsTranslatorsCall->getMessageId();
        $domain = $jsTranslatorsCall->getDomain();

        if ($catalogue->defines($messageId, $domain)) {
            return $catalogue->get((string)$messageId, $domain);
        } elseif ($locale === Translator::SOURCE_LOCALE) {
            return $messageId;
        } else {
            return $this->markAsNotTranslated((string)$messageId);
        }
    }

    /**
     * @param string $messageId
     * @return string
     */
    private function markAsNotTranslated($messageId)
    {
        $pluralizationParts = explode('|', $messageId);
        $markedMessages = [];
        foreach ($pluralizationParts as $part) {
            $endBracePosition = strpos($part, '}');
            $endBracketPosition = strpos($part, ']');
            if ($endBracePosition !== false) {
                $notTranslatedPrefixPosition = $endBracePosition + 1;
            } elseif ($endBracketPosition !== false) {
                $notTranslatedPrefixPosition = $endBracketPosition + 1;
            } else {
                $notTranslatedPrefixPosition = 0;
            }
            $markedMessages[] = substr_replace($part, Translator::NOT_TRANSLATED_PREFIX, $notTranslatedPrefixPosition, 0);
        }

        return implode('|', $markedMessages);
    }
}
