<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Component\Doctrine\GroupedScalarHydrator;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ParameterFilterChoiceRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProductRepository $productRepository
    ) {
        $this->em = $em;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $locale
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoice[]
     */
    public function getParameterFilterChoicesInCategory(
        $domainId,
        PricingGroup $pricingGroup,
        $locale,
        Category $category
    ) {
        $productsQueryBuilder = $this->productRepository->getListableInCategoryQueryBuilder(
            $domainId,
            $pricingGroup,
            $category
        );

        $productsQueryBuilder
            ->select('MIN(p), pp, pv')
            ->join(ProductParameterValue::class, 'ppv', Join::WITH, 'ppv.product = p')
            ->join(Parameter::class, 'pp', Join::WITH, 'pp = ppv.parameter')
            ->join(ParameterValue::class, 'pv', Join::WITH, 'pv = ppv.value AND pv.locale = :locale')
            ->groupBy('pp, pv')
            ->resetDQLPart('orderBy')
            ->setParameter('locale', $locale);

        $rows = $productsQueryBuilder->getQuery()->execute(null, GroupedScalarHydrator::HYDRATION_MODE);

        $visibleParametersIndexedById = $this->getVisibleParametersIndexedByIdOrderedByName($rows, $locale);
        $parameterValuesIndexedByParameterId = $this->getParameterValuesIndexedByParameterIdOrderedByValueText($rows, $locale);
        $parameterFilterChoices = [];

        foreach ($visibleParametersIndexedById as $parameterId => $parameter) {
            if (array_key_exists($parameterId, $parameterValuesIndexedByParameterId)) {
                $parameterFilterChoices[] = new ParameterFilterChoice(
                    $parameter,
                    $parameterValuesIndexedByParameterId[$parameterId]
                );
            }
        }

        return $parameterFilterChoices;
    }

    /**
     * @param array $rows
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter[]
     */
    private function getVisibleParametersIndexedByIdOrderedByName(array $rows, $locale)
    {
        $parameterIds = [];
        foreach ($rows as $row) {
            $parameterIds[$row['pp']['id']] = $row['pp']['id'];
        }

        $parametersQueryBuilder = $this->em->createQueryBuilder()
            ->select('pp, pt')
            ->from(Parameter::class, 'pp')
            ->join('pp.translations', 'pt', Join::WITH, 'pt.locale = :locale')
            ->where('pp.id IN (:parameterIds)')
            ->andWhere('pp.visible = true')
            ->orderBy('pt.name', 'asc');
        $parametersQueryBuilder->setParameter('parameterIds', $parameterIds);
        $parametersQueryBuilder->setParameter('locale', $locale);
        $parameters = $parametersQueryBuilder->getQuery()->execute();

        $parametersIndexedById = [];
        foreach ($parameters as $parameter) {
            /* @var $parameter \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter */
            $parametersIndexedById[$parameter->getId()] = $parameter;
        }

        return $parametersIndexedById;
    }

    /**
     * @param array $rows
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[][]
     */
    private function getParameterValuesIndexedByParameterIdOrderedByValueText(array $rows, $locale)
    {
        $parameterIdsByValueId = [];
        foreach ($rows as $row) {
            $valueId = $row['pv']['id'];
            $parameterId = $row['pp']['id'];
            $parameterIdsByValueId[$valueId][] = $parameterId;
        }

        $valuesIndexedById = $this->getParameterValuesIndexedByIdOrderedByText($rows, $locale);

        $valuesIndexedByParameterId = [];
        foreach ($valuesIndexedById as $valueId => $value) {
            foreach ($parameterIdsByValueId[$valueId] as $parameterId) {
                $valuesIndexedByParameterId[$parameterId][] = $value;
            }
        }

        return $valuesIndexedByParameterId;
    }

    /**
     * @param array $rows
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[]
     */
    private function getParameterValuesIndexedByIdOrderedByText(array $rows, $locale)
    {
        $valueIds = [];
        foreach ($rows as $row) {
            $valueId = $row['pv']['id'];
            $valueIds[$valueId] = $valueId;
        }

        $valuesQueryBuilder = $this->em->createQueryBuilder()
            ->select('pv')
            ->from(ParameterValue::class, 'pv')
            ->where('pv.id IN (:valueIds)')
            ->andWhere('pv.locale = :locale')
            ->orderBy('pv.text', 'asc');
        $valuesQueryBuilder->setParameter('valueIds', $valueIds);
        $valuesQueryBuilder->setParameter('locale', $locale);
        $values = $valuesQueryBuilder->getQuery()->execute();

        $valuesIndexedById = [];
        foreach ($values as $value) {
            /* @var $value \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue */
            $valuesIndexedById[$value->getId()] = $value;
        }

        return $valuesIndexedById;
    }
}
