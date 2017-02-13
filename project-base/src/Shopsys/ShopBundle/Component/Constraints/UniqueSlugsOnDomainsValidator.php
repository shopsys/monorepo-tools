<?php

namespace Shopsys\ShopBundle\Component\Constraints;

use Shopsys\ShopBundle\Component\Constraints\UniqueSlugsOnDomains;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Form\FriendlyUrlType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueSlugsOnDomainsValidator extends ConstraintValidator
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    public function __construct(Domain $domain, DomainRouterFactory $domainRouterFactory)
    {
        $this->domain = $domain;
        $this->domainRouterFactory = $domainRouterFactory;
    }

    /**
     * @param array $values
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($values, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueSlugsOnDomains) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, UniqueSlugsOnDomains::class);
        }

        $this->validateDuplication($values, $constraint);
        $this->validateExists($values, $constraint);
    }

    /**
     * @param array $values
     * @param \Shopsys\ShopBundle\Component\Constraints\UniqueSlugsOnDomains $constraint
     */
    private function validateDuplication(array $values, UniqueSlugsOnDomains $constraint)
    {
        $slugCountsOnDomain = $this->getSlugCountsOnDomain($values);
        foreach ($slugCountsOnDomain as $domainId => $countBySlug) {
            $domainConfig = $this->domain->getDomainConfigById($domainId);
            foreach ($countBySlug as $slug => $count) {
                if ($count > 1) {
                    $this->context->addViolation(
                        $constraint->messageDuplicate,
                        [
                            '{{ url }}' => $domainConfig->getUrl() . '/' . $slug,
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param array $values
     * @param \Shopsys\ShopBundle\Component\Constraints\UniqueSlugsOnDomains $constraint
     */
    private function validateExists($values, UniqueSlugsOnDomains $constraint)
    {
        foreach ($values as $urlData) {
            $domainId = $urlData[FriendlyUrlType::FIELD_DOMAIN];
            $domainConfig = $this->domain->getDomainConfigById($domainId);
            $slug = $urlData[FriendlyUrlType::FIELD_SLUG];

            $domainRouter = $this->domainRouterFactory->getRouter($domainId);
            try {
                $domainRouter->match('/' . $slug);
            } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
                continue;
            }

            $this->context->addViolation(
                $constraint->message,
                [
                    '{{ url }}' => $domainConfig->getUrl() . '/' . $slug,
                ]
            );
        }
    }

    /**
     * @param array $values
     * @return int[domainId][slug]
     */
    private function getSlugCountsOnDomain(array $values)
    {
        $slugCountsOnDomain = [];
        foreach ($values as $urlData) {
            $domainId = $urlData[FriendlyUrlType::FIELD_DOMAIN];
            $slug = $urlData[FriendlyUrlType::FIELD_SLUG];
            if (!array_key_exists($domainId, $slugCountsOnDomain)) {
                $slugCountsOnDomain[$domainId] = [];
            }
            if (!array_key_exists($slug, $slugCountsOnDomain[$domainId])) {
                $slugCountsOnDomain[$domainId][$slug] = 0;
            }

            $slugCountsOnDomain[$domainId][$slug]++;
        }

        return $slugCountsOnDomain;
    }
}
