<?php

namespace Shopsys\FrameworkBundle\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\DependencyInjection\LazyLoadingFragmentHandler;

class FragmentHandler extends LazyLoadingFragmentHandler
{
    /**
     * Support redirect responses in fragments (eg. subrequests). Fragments can only return 2xx HTTP codes by default.
     * Redirect is handled in @see \Shopsys\FrameworkBundle\Component\HttpFoundation\SubRequestListener::onKernelResponse().
     *
     * {@inheritdoc}
     */
    protected function deliver(Response $response)
    {
        if (!$response->isRedirection()) {
            return parent::deliver($response);
        }

        /** Same response handling as in @see \Symfony\Component\HttpKernel\Fragment\FragmentHandler::deliver(). */
        if (!$response instanceof StreamedResponse) {
            return $response->getContent();
        }

        $response->sendContent();
    }

    /**
     * Option "ignore_errors" has different default value based on Kernel's $debug argument.
     * This leads to inconsistent error handling in development and production environment.
     *
     * {@inheritdoc}
     */
    public function render($uri, $renderer = 'inline', array $options = [])
    {
        if (!isset($options['ignore_errors'])) {
            $options['ignore_errors'] = false;
        }

        return parent::render($uri, $renderer, $options);
    }
}
