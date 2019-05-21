<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain\Twig;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader as BaseFilesystemLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class FilesystemLoader extends BaseFilesystemLoader
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain|null
     */
    protected $domain;

    /**
     * @param \Symfony\Component\Config\FileLocatorInterface $locator
     * @param \Symfony\Component\Templating\TemplateNameParserInterface $parser
     * @param string|null $rootPath
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain|null $domain
     */
    public function __construct(
        FileLocatorInterface $locator,
        TemplateNameParserInterface $parser,
        ?string $rootPath = null,
        ?Domain $domain = null
    ) {
        $this->domain = $domain;
        parent::__construct($locator, $parser, $rootPath);
        $this->assertDomainDependency();
    }

    /**
     * When exists a template with filename.{designId}.html.twig, then it is automatically used
     * on domain with this {designId} whenever template named filename.html.twig is on input
     *
     * @inheritdoc
     */
    protected function findTemplate($template, $throw = true)
    {
        $templateName = (string)$template;
        $multidesignTemplate = null;
        if (strpos($templateName, '@ShopsysShop/Front/') === 0) {
            $multidesignTemplate = $this->findMultidesignTemplate($templateName);
        }

        if ($multidesignTemplate !== null) {
            return $multidesignTemplate;
        }

        return parent::findTemplate($templateName);
    }

    protected function assertDomainDependency()
    {
        if (!($this->domain instanceof Domain)) {
            $message = sprintf('Template loader needs an instance of %s class', Domain::class);
            throw new \Shopsys\FrameworkBundle\Component\Domain\Multidomain\Twig\Exception\MissingDependencyException($message);
        }
    }

    /**
     * @param string $templateName
     * @return string|null
     */
    protected function findMultidesignTemplate($templateName)
    {
        try {
            $designId = $this->domain->getDesignId();
            if ($designId !== null) {
                $multidesignTemplateName = preg_replace('/^(.*)(\.[^\.]*\.twig)$/', '$1.' . $designId . '$2', $templateName);
                try {
                    return parent::findTemplate($multidesignTemplateName);
                } catch (\Twig_Error_Loader $loaderException) {
                    if (strpos($loaderException->getMessage(), 'Unable to find template') !== 0) {
                        $message = sprintf('Unexpected exception when trying to load multidesign template `%s`', $multidesignTemplateName);
                        throw new \Twig_Error_Loader($message, -1, null, $loaderException);
                    }
                }
            }
        } catch (\Shopsys\FrameworkBundle\Component\Domain\Exception\NoDomainSelectedException $ex) {
            return null;
        }

        return null;
    }
}
