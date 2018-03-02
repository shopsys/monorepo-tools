<?php

namespace Shopsys\FrameworkBundle\Component\Sitemap;

class SitemapService
{
    /**
     * @param int $domainId
     * @return string
     */
    public function getSitemapFilePrefixForDomain($domainId)
    {
        return 'domain_' . $domainId . '_sitemap';
    }
}
