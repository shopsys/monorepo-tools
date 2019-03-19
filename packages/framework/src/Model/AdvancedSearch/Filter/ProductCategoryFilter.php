<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomain;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProductCategoryFilter implements AdvancedSearchFilterInterface
{
    public const NAME = 'productCategory';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    protected $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(CategoryFacade $categoryFacade, Domain $domain)
    {
        $this->categoryFacade = $categoryFacade;
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOperators()
    {
        return [
            self::OPERATOR_IS,
            self::OPERATOR_IS_NOT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormType()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormOptions()
    {
        return [
            'expanded' => false,
            'multiple' => false,
            'choices' => $this->categoryFacade->getTranslatedAll($this->domain->getCurrentDomainConfig()),
            'choice_label' => 'name',
            'choice_value' => 'id',
            'attr' => ['class' => 'js-autocomplete-selectbox'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData)
    {
        $isCategory = [];
        $isNotCategory = [];
        foreach ($rulesData as $ruleData) {
            if ($ruleData->operator === self::OPERATOR_IS) {
                $isCategory[] = $ruleData->value;
            } elseif ($ruleData->operator === self::OPERATOR_IS_NOT) {
                $isNotCategory[] = $ruleData->value;
            }
        }
        if (count($isCategory) + count($isNotCategory) > 0) {
            $subQuery = 'SELECT IDENTITY(%s.product) FROM ' . ProductCategoryDomain::class . ' %1$s WHERE %1$s.category IN (:%s)';

            if (count($isCategory) > 0) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('p.id', sprintf($subQuery, 'pcd_is', 'isCategory')));
                $queryBuilder->setParameter('isCategory', $isCategory);
            }
            if (count($isNotCategory) > 0) {
                $queryBuilder->andWhere($queryBuilder->expr()->notIn('p.id', sprintf($subQuery, 'pcd_not', 'isNotCategory')));
                $queryBuilder->setParameter('isNotCategory', $isNotCategory);
            }
        }
    }
}
