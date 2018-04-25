<?php

namespace Shopsys\FrameworkBundle\Component\Response;

use DOMDocument;
use SplFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class XmlStreamedResponse extends StreamedResponse
{
    /**
     * @param $xmlContent
     * @param $fileName
     */
    public function __construct($xmlContent, $fileName)
    {
        $callback = $this->createCallback($xmlContent);
        $headers = [
            'Content-Type' => 'xml',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        parent::__construct($callback, 200, $headers);
    }

    /**
     * @param string $xmlContent
     * @return \Closure
     */
    private function createCallback($xmlContent)
    {
        $xmlContent = $this->normalizeXml($xmlContent);

        return function () use (&$xmlContent) {
            $output = new SplFileObject('php://output', 'w+');
            $output->fwrite($xmlContent);
        };
    }

    /**
     * @param string $content
     * @return string
     */
    private function normalizeXml($content)
    {
        $document = new DOMDocument('1.0');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->loadXML($content);
        $generatedXml = $document->saveXML();

        return $generatedXml;
    }
}
