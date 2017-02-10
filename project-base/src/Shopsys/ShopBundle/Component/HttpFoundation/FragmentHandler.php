<?php

namespace Shopsys\ShopBundle\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\DependencyInjection\LazyLoadingFragmentHandler;

class FragmentHandler extends LazyLoadingFragmentHandler
{

    /**
     * Copy-pasted & edited from Symfony\Component\HttpKernel\Fragment\FragmentHandler::deliver().
     *
     * {@inheritdoc}
     */
    protected function deliver(Response $response) {
        // Redirect response in fragment is OK, because SubRequestListener will do the redirection
        // when handling the master request.
        if (!$response->isSuccessful() && !$response->isRedirection()) {
            $message = sprintf(
                'Error when rendering response (Status code is %s).',
                $response->getStatusCode()
            );
            throw new \RuntimeException($message);
        }

        if (!$response instanceof StreamedResponse) {
            return $response->getContent();
        }

        $response->sendContent();
    }

    /**
     * {@inheritdoc}
     */
    public function render($uri, $renderer = 'inline', array $options = []) {
        if (!isset($options['ignore_errors'])) {
            $options['ignore_errors'] = false;
        }

        return parent::render($uri, $renderer, $options);
    }
}
