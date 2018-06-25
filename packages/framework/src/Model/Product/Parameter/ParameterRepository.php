<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ParameterRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueFactoryInterface
     */
    protected $parameterValueFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueFactoryInterface $parameterValueFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterValueFactoryInterface $parameterValueFactory
    ) {
        $this->em = $entityManager;
        $this->parameterValueFactory = $parameterValueFactory;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getParameterRepository()
    {
        return $this->em->getRepository(Parameter::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getParameterValueRepository()
    {
        return $this->em->getRepository(ParameterValue::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getProductParameterValueRepository()
    {
        return $this->em->getRepository(ProductParameterValue::class);
    }

    /**
     * @param int $parameterId
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter|null
     */
    public function findById($parameterId)
    {
        return $this->getParameterRepository()->find($parameterId);
    }

    /**
     * @param int $parameterId
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter
     */
    public function getById($parameterId)
    {
        $parameter = $this->findById($parameterId);

        if ($parameter === null) {
            $message = 'Parameter with ID ' . $parameterId . ' not found.';
            throw new \Shopsys\FrameworkBundle\Model\Product\Parameter\Exception\ParameterNotFoundException($message);
        }

        return $parameter;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter[]
     */
    public function getAll()
    {
        return $this->getParameterRepository()->findBy([], ['id' => 'asc']);
    }

    /**
     * @param string $valueText
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue
     */
    public function findOrCreateParameterValueByValueTextAndLocale($valueText, $locale)
    {
        $parameterValue = $this->getParameterValueRepository()->findOneBy([
            'text' => $valueText,
            'locale' => $locale,
        ]);

        if ($parameterValue === null) {
            $parameterValueData = new ParameterValueData();
            $parameterValueData->text = $valueText;
            $parameterValueData->locale = $locale;
            $parameterValue = $this->parameterValueFactory->create($parameterValueData);
            $this->em->persist($parameterValue);
            // Doctrine's identity map is not cache.
            // We have to flush now, so that next findOneBy() finds new ParameterValue.
            $this->em->flush();
        }

        return $parameterValue;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getProductParameterValuesByProductQueryBuilder(Product $product)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('ppv')
            ->from(ProductParameterValue::class, 'ppv')
            ->where('ppv.product = :product_id')
            ->setParameter('product_id', $product->getId());

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param string $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getProductParameterValuesByProductSortedByNameQueryBuilder(Product $product, $locale)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('ppv')
            ->from(ProductParameterValue::class, 'ppv')
            ->join('ppv.parameter', 'p')
            ->join('p.translations', 'pt')
            ->where('ppv.product = :product_id')
            ->andWhere('pt.locale = :locale')
            ->setParameters([
                'product_id' => $product->getId(),
                'locale' => $locale,
            ])
            ->orderBy('pt.name');

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue[]
     */
    public function getProductParameterValuesByProduct(Product $product)
    {
        $queryBuilder = $this->getProductParameterValuesByProductQueryBuilder($product);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue[]
     */
    public function getProductParameterValuesByProductSortedByName(Product $product, $locale)
    {
        $queryBuilder = $this->getProductParameterValuesByProductSortedByNameQueryBuilder($product, $locale);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     * @param string $locale
     * @return string[][]
     */
    public function getParameterValuesIndexedByProductIdAndParameterNameForProducts(array $products, $locale)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('IDENTITY(ppv.product) as productId', 'pt.name', 'pv.text')
            ->from(ProductParameterValue::class, 'ppv')
            ->join('ppv.parameter', 'p')
            ->join('p.translations', 'pt')
            ->join('ppv.value', 'pv')
            ->where('ppv.product IN (:products)')
            ->andWhere('pv.locale = :locale')
            ->andWhere('pt.locale = :locale')
            ->setParameters([
                'products' => $products,
                'locale' => $locale,
            ]);

        $productIdsAndParameterNamesAndValues = $queryBuilder->getQuery()->execute(null, Query::HYDRATE_ARRAY);

        return $this->getParameterValuesIndexedByProductIdAndParameterName($productIdsAndParameterNamesAndValues);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter $parameter
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue[]
     */
    public function getProductParameterValuesByParameter(Parameter $parameter)
    {
        return $this->getProductParameterValueRepository()->findBy([
            'parameter' => $parameter,
        ]);
    }

    /**
     * @param string[] $namesByLocale
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter|null
     */
    public function findParameterByNames(array $namesByLocale)
    {
        $queryBuilder = $this->getParameterRepository()->createQueryBuilder('p');
        $index = 0;
        foreach ($namesByLocale as $locale => $name) {
            $alias = 'pt' . $index;
            $localeParameterName = 'locale' . $index;
            $nameParameterName = 'name' . $index;
            $queryBuilder->join(
                'p.translations',
                $alias,
                Join::WITH,
                'p = ' . $alias . '.translatable
                    AND ' . $alias . '.locale = :' . $localeParameterName . '
                    AND ' . $alias . '.name = :' . $nameParameterName
            );
            $queryBuilder->setParameter($localeParameterName, $locale);
            $queryBuilder->setParameter($nameParameterName, $name);
            $index++;
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $productIdsAndParameterNamesAndValues
     * @return string[][]
     */
    protected function getParameterValuesIndexedByProductIdAndParameterName(array $productIdsAndParameterNamesAndValues)
    {
        $productParameterValuesIndexedByProductIdAndParameterName = [];
        foreach ($productIdsAndParameterNamesAndValues as $productIdAndParameterNameAndValue) {
            $parameterName = $productIdAndParameterNameAndValue['name'];
            $productId = $productIdAndParameterNameAndValue['productId'];
            $parameterValue = $productIdAndParameterNameAndValue['text'];
            $productParameterValuesIndexedByProductIdAndParameterName[$productId][$parameterName] = $parameterValue;
        }

        return $productParameterValuesIndexedByProductIdAndParameterName;
    }
}
