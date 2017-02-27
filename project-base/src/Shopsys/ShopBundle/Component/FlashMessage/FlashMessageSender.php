<?php

namespace Shopsys\ShopBundle\Component\FlashMessage;

use Twig_Environment;

class FlashMessageSender
{
    /**
     * @var \Shopsys\ShopBundle\Component\FlashMessage\Bag
     */
    private $flashMessageBag;

    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    public function __construct(
        Bag $flashMessageBag,
        Twig_Environment $twigEnvironment
    ) {
        $this->flashMessageBag = $flashMessageBag;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @param string $template
     * @param array $parameters
     */
    public function addErrorFlashTwig($template, $parameters = [])
    {
        $message = $this->renderStringTwigTemplate($template, $parameters);
        $this->flashMessageBag->addError($message, false);
    }

    /**
     * @param string $template
     * @param array $parameters
     */
    public function addInfoFlashTwig($template, $parameters = [])
    {
        $message = $this->renderStringTwigTemplate($template, $parameters);
        $this->flashMessageBag->addInfo($message, false);
    }

    /**
     * @param string $template
     * @param array $parameters
     */
    public function addSuccessFlashTwig($template, $parameters = [])
    {
        $message = $this->renderStringTwigTemplate($template, $parameters);
        $this->flashMessageBag->addSuccess($message, false);
    }

    /**
     * @param string $template
     * @param array $parameters
     * @return string
     */
    private function renderStringTwigTemplate($template, array $parameters)
    {
        $twigTemplate = $this->twigEnvironment->createTemplate($template);

        return $twigTemplate->render($parameters);
    }

    /**
     * @param string|array $message
     */
    public function addErrorFlash($message)
    {
        $this->flashMessageBag->addError($message, true);
    }

    /**
     * @param string|array $message
     */
    public function addInfoFlash($message)
    {
        $this->flashMessageBag->addInfo($message, true);
    }

    /**
     * @param string|array $message
     */
    public function addSuccessFlash($message)
    {
        $this->flashMessageBag->addSuccess($message, true);
    }
}
