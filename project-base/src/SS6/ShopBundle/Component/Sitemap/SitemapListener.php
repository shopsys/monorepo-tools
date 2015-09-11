<?php

namespace SS6\ShopBundle\Component\Sitemap;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapListener implements SitemapListenerInterface {

	public function populateSitemap(SitemapPopulateEvent $event) {
		$section = $event->getSection();

		if ($section === null) {
			throw new \Exception('Invalid section');
		}

		$event->getGenerator()->addUrl(
			new UrlConcrete('http://www.example' . $section . '.com', new \DateTime(), UrlConcrete::CHANGEFREQ_HOURLY, 1),
			$section
		);

		$event->getGenerator()->addUrl(
			new UrlConcrete('http://www.example' . $section . '.com/test/', new \DateTime(), UrlConcrete::CHANGEFREQ_DAILY, 1),
			$section
		);
	}

}
