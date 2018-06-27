<?php

namespace Shopsys\FrameworkBundle\Component\Response;

use Shopsys\FrameworkBundle\Component\Xml\XmlNormalizer;
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
        $xmlContent = XmlNormalizer::normalizeXml($xmlContent);

        return function () use (&$xmlContent) {
            $output = new SplFileObject('php://output', 'w+');
            $output->fwrite($xmlContent);
        };
    }
}
