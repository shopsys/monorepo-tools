<?php

namespace Shopsys\ShopBundle\Twig;

use Twig_Environment;
use Twig_SimpleFilter;

class TranslationExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('transHtml', [$this, 'transHtml'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
            new Twig_SimpleFilter('transchoiceHtml', [$this, 'transchoiceHtml'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translation';
    }

    /**
     * Similar to "trans" filter, the message is not escaped in html but all translation arguments are
     *
     * Helpful for protection from XSS when providing user input as translation argument
     * @see \Symfony\Bridge\Twig\Extension\TranslationExtension::trans()
     *
     * @param \Twig_Environment $twig
     * @param string $message
     * @param array $arguments
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public function transHtml(Twig_Environment $twig, $message, array $arguments = [], $domain = null, $locale = null)
    {
        $defaultTransCallable = $twig->getFilter('trans')->getCallable();
        $escapedArguments = $this->getEscapedElements($twig, $arguments);

        return $defaultTransCallable($message, $escapedArguments, $domain, $locale);
    }

    /**
     * Similar to "transchoice" filter, the message is not escaped in html but all translation arguments are
     *
     * Helpful for protection from XSS when providing user input as translation argument
     * @see \Symfony\Bridge\Twig\Extension\TranslationExtension::transchoice()
     *
     * @param \Twig_Environment $twig
     * @param string $message
     * @param int $count
     * @param array $arguments
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public function transchoiceHtml(Twig_Environment $twig, $message, $count, array $arguments = [], $domain = null, $locale = null)
    {
        $defaultTranschoiceCallable = $twig->getFilter('transchoice')->getCallable();
        $escapedArguments = $this->getEscapedElements($twig, $arguments);

        return $defaultTranschoiceCallable($message, $count, $escapedArguments, $domain, $locale);
    }

    /**
     * Escapes all elements in array with default twig "escape" filter
     *
     * @param \Twig_Environment $twig
     * @param array $elements
     * @return array
     */
    private function getEscapedElements(Twig_Environment $twig, array $elements)
    {
        $defaultEscapeFilterCallable = $twig->getFilter('escape')->getCallable();
        $escapedElements = [];
        foreach ($elements as $key => $element) {
            $escapedElements[$key] = $defaultEscapeFilterCallable($twig, $element);
        }

        return $escapedElements;
    }
}
