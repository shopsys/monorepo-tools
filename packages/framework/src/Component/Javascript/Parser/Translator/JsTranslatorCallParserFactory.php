<?php

namespace Shopsys\FrameworkBundle\Component\Javascript\Parser\Translator;

use Shopsys\FrameworkBundle\Component\Javascript\Parser\JsFunctionCallParser;
use Shopsys\FrameworkBundle\Component\Javascript\Parser\JsStringParser;
use Shopsys\FrameworkBundle\Component\Translation\TransMethodSpecification;

class JsTranslatorCallParserFactory
{
    const METHOD_NAME_TRANS = 'Shopsys.translator.trans';
    const METHOD_NAME_TRANS_CHOICE = 'Shopsys.translator.transChoice';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsFunctionCallParser
     */
    private $jsFunctionCallParser;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsStringParser
     */
    private $jsStringParser;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsFunctionCallParser $jsFunctionCallParser
     * @param \Shopsys\FrameworkBundle\Component\Javascript\Parser\JsStringParser $jsStringParser
     */
    public function __construct(
        JsFunctionCallParser $jsFunctionCallParser,
        JsStringParser $jsStringParser
    ) {
        $this->jsFunctionCallParser = $jsFunctionCallParser;
        $this->jsStringParser = $jsStringParser;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser
     */
    public function create()
    {
        $transMethodSpecifications = [
            new TransMethodSpecification(self::METHOD_NAME_TRANS, 0, 2),
            new TransMethodSpecification(self::METHOD_NAME_TRANS_CHOICE, 0, 3),
        ];

        return new JsTranslatorCallParser(
            $this->jsFunctionCallParser,
            $this->jsStringParser,
            $transMethodSpecifications
        );
    }
}
