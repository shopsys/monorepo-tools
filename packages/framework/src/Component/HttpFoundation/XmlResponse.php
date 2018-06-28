<?php

namespace Shopsys\FrameworkBundle\Component\HttpFoundation;

use Shopsys\FrameworkBundle\Component\Xml\XmlNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class XmlResponse extends Response
{
    /**
     * @param string $fileName
     * @param string $content
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getXmlResponse($fileName, $content)
    {
        $xmlContent = XmlNormalizer::normalizeXml($content);
        $response = new Response($xmlContent);

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Type', 'xml');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
