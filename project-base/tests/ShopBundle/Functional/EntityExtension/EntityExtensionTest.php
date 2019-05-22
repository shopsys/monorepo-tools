<?php

namespace Tests\ShopBundle\Functional\EntityExtension;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\ProductTranslation;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\Order as ExtendedOrder;
use Shopsys\ShopBundle\Model\Product\Brand\Brand as ExtendedBrand;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Functional\EntityExtension\Model\CategoryManyToManyBidirectionalEntity;
use Tests\ShopBundle\Functional\EntityExtension\Model\CategoryOneToManyBidirectionalEntity;
use Tests\ShopBundle\Functional\EntityExtension\Model\CategoryOneToOneBidirectionalEntity;
use Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedCategory;
use Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedOrderItem;
use Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct;
use Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProductTranslation;
use Tests\ShopBundle\Functional\EntityExtension\Model\ProductManyToManyBidirectionalEntity;
use Tests\ShopBundle\Functional\EntityExtension\Model\ProductOneToManyBidirectionalEntity;
use Tests\ShopBundle\Functional\EntityExtension\Model\ProductOneToOneBidirectionalEntity;
use Tests\ShopBundle\Functional\EntityExtension\Model\UnidirectionalEntity;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class EntityExtensionTest extends TransactionFunctionalTestCase
{
    protected const MAIN_PRODUCT_ID = 1;
    protected const ONE_TO_ONE_SELF_REFERENCING_PRODUCT_ID = 2;
    protected const ONE_TO_MANY_SELF_REFERENCING_PRODUCT_ID = 3;
    protected const MANY_TO_MANY_SELF_REFERENCING_PRODUCT_ID = 4;

    protected const MAIN_CATEGORY_ID = 1;
    protected const ONE_TO_ONE_SELF_REFERENCING_CATEGORY_ID = 2;
    protected const ONE_TO_MANY_SELF_REFERENCING_CATEGORY_ID = 3;
    protected const MANY_TO_MANY_SELF_REFERENCING_CATEGORY_ID = 4;

    protected const ORDER_ITEM_ID = 1;

    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityManagerDecorator
     */
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->em = $this->getEntityManager();
        $this->registerTestEntities();

        $entityExtensionMap = [
            Product::class => ExtendedProduct::class,
            Category::class => ExtendedCategory::class,
            OrderItem::class => ExtendedOrderItem::class,
            Brand::class => ExtendedBrand::class,
            Order::class => ExtendedOrder::class,
            ProductTranslation::class => ExtendedProductTranslation::class,
        ];

        $applicationEntityExtensionMap = $this->getContainer()->getParameter('shopsys.entity_extension.map');

        foreach ($applicationEntityExtensionMap as $baseClass => $extendedClass) {
            if (!array_key_exists($baseClass, $entityExtensionMap)) {
                $entityExtensionMap[$baseClass] = $extendedClass;
            }
        }

        $newEntities = [
            UnidirectionalEntity::class,
            ProductOneToOneBidirectionalEntity::class,
            ProductManyToManyBidirectionalEntity::class,
            ProductOneToManyBidirectionalEntity::class,
            CategoryOneToOneBidirectionalEntity::class,
            CategoryManyToManyBidirectionalEntity::class,
            CategoryOneToManyBidirectionalEntity::class,
        ];

        $this->overwriteEntityExtensionMapInServicesInContainer($entityExtensionMap);

        $testEntities = array_merge($newEntities, array_values($entityExtensionMap));
        $metadata = $this->getMetadata($testEntities);

        $this->generateProxies($metadata);
        $this->updateDatabaseSchema($metadata);
    }

    public function registerTestEntities(): void
    {
        $driver = new AnnotationDriver(new AnnotationReader(), __DIR__ . '/Model');

        $configuration = $this->em->getConfiguration();
        $metadataDriverChain = $configuration->getMetadataDriverImpl();
        if ($metadataDriverChain instanceof MappingDriverChain) {
            $metadataDriverChain->addDriver($driver, 'Tests\\ShopBundle\\Functional\\EntityExtension');
        } else {
            $this->fail(sprintf('Metadata driver must be type of %s', MappingDriverChain::class));
        }
    }

    /**
     * @param string[] $entityExtensionMap
     */
    public function overwriteEntityExtensionMapInServicesInContainer(array $entityExtensionMap): void
    {
        /** @var \Tests\ShopBundle\Functional\EntityExtension\OverwritableLoadORMMetadataSubscriber $loadORMMetadataSubscriber */
        $loadORMMetadataSubscriber = $this->getContainer()->get('joschi127_doctrine_entity_override.event_subscriber.load_orm_metadata');
        /** @var \Tests\ShopBundle\Functional\EntityExtension\OverwritableEntityNameResolver $entityNameResolver */
        $entityNameResolver = $this->getContainer()->get(EntityNameResolver::class);

        $loadORMMetadataSubscriber->overwriteEntityExtensionMap($entityExtensionMap);
        $entityNameResolver->overwriteEntityExtensionMap($entityExtensionMap);
    }

    /**
     * @param string[] $entities
     * @return \Doctrine\ORM\Mapping\ClassMetadata[]
     */
    public function getMetadata(array $entities): array
    {
        return array_map(function (string $entity) {
            return $this->em->getClassMetadata($entity);
        }, $entities);
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata[] $metadata
     */
    public function generateProxies(array $metadata): void
    {
        $this->em->getProxyFactory()->generateProxyClasses($metadata);
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata[] $metadata
     */
    public function updateDatabaseSchema(array $metadata): void
    {
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->updateSchema($metadata);
    }

    /**
     * Test everything at once because setUp is slow
     */
    public function testAll(): void
    {
        $this->doTestExtendedProductPersistence();
        $this->doTestExtendedCategoryPersistence();
        $this->doTestExtendedOrderItemsPersistence();

        $this->doTestExtendedEntityInstantiation(Product::class, ExtendedProduct::class, self::MAIN_PRODUCT_ID);
        $this->doTestExtendedEntityInstantiation(Category::class, ExtendedCategory::class, self::MAIN_CATEGORY_ID);
        $this->doTestExtendedEntityInstantiation(OrderItem::class, ExtendedOrderItem::class, self::ORDER_ITEM_ID);
        $this->doTestExtendedEntityInstantiation(ProductTranslation::class, ExtendedProductTranslation::class, self::MAIN_PRODUCT_ID);
    }

    private function doTestExtendedProductPersistence(): void
    {
        $product = $this->getProduct(self::MAIN_PRODUCT_ID);

        $product->setStringField('main product string');

        $foundManyToOneUnidirectionalEntity = new UnidirectionalEntity('many-to-one unidirectional');
        $this->em->persist($foundManyToOneUnidirectionalEntity);
        $product->setManyToOneUnidirectionalEntity($foundManyToOneUnidirectionalEntity);

        $oneToOneUnidirectionalEntity = new UnidirectionalEntity('one-to-one unidirectional');
        $this->em->persist($oneToOneUnidirectionalEntity);
        $product->setOneToOneUnidirectionalEntity($oneToOneUnidirectionalEntity);

        $oneToOneBidirectionalEntity = new ProductOneToOneBidirectionalEntity('one-to-one bidirectional');
        $this->em->persist($oneToOneBidirectionalEntity);
        $product->setOneToOneBidirectionalEntity($oneToOneBidirectionalEntity);

        $oneToOneSelfReferencingEntity = $this->getProduct(self::ONE_TO_ONE_SELF_REFERENCING_PRODUCT_ID);
        $product->setOneToOneSelfReferencingEntity($oneToOneSelfReferencingEntity);

        $oneToManyBidirectionalEntity = new ProductOneToManyBidirectionalEntity('one-to-many bidirectional');
        $this->em->persist($oneToManyBidirectionalEntity);
        $product->addOneToManyBidirectionalEntity($oneToManyBidirectionalEntity);

        $oneToManyUnidirectionalWithJoinTableEntity = new UnidirectionalEntity('one-to-many unidirectional with join table');
        $this->em->persist($oneToManyUnidirectionalWithJoinTableEntity);
        $product->addOneToManyUnidirectionalWithJoinTableEntity($oneToManyUnidirectionalWithJoinTableEntity);

        $oneToManySelfReferencingEntity = $this->getProduct(self::ONE_TO_MANY_SELF_REFERENCING_PRODUCT_ID);
        $product->addOneToManySelfReferencingEntity($oneToManySelfReferencingEntity);

        $manyToManyUnidirectionalEntity = new UnidirectionalEntity('many-to-many unidirectional');
        $this->em->persist($manyToManyUnidirectionalEntity);
        $product->addManyToManyUnidirectionalEntity($manyToManyUnidirectionalEntity);

        $manyToManyBidirectionalEntity = new ProductManyToManyBidirectionalEntity('many-to-many bidirectional');
        $this->em->persist($manyToManyBidirectionalEntity);
        $product->addManyToManyBidirectionalEntity($manyToManyBidirectionalEntity);

        $manyToManySelfReferencingEntity = $this->getProduct(self::MANY_TO_MANY_SELF_REFERENCING_PRODUCT_ID);
        $product->addManyToManySelfReferencingEntity($manyToManySelfReferencingEntity);

        $this->em->flush();
        $this->em->clear();

        $foundProduct = $this->getProduct(self::MAIN_PRODUCT_ID);

        $this->assertSame('main product string', $foundProduct->getStringField());

        $foundManyToOneUnidirectionalEntity = $foundProduct->getManyToOneUnidirectionalEntity();
        $this->assertInstanceOf(UnidirectionalEntity::class, $foundManyToOneUnidirectionalEntity);
        $this->assertSame('many-to-one unidirectional', $foundManyToOneUnidirectionalEntity->getName());

        $foundOneToOneUnidirectionalEntity = $foundProduct->getOneToOneUnidirectionalEntity();
        $this->assertInstanceOf(UnidirectionalEntity::class, $foundOneToOneUnidirectionalEntity);
        $this->assertSame('one-to-one unidirectional', $foundOneToOneUnidirectionalEntity->getName());

        $foundOneToOneBidirectionalEntity = $foundProduct->getOneToOneBidirectionalEntity();
        $this->assertInstanceOf(ProductOneToOneBidirectionalEntity::class, $foundOneToOneBidirectionalEntity);
        $this->assertSame('one-to-one bidirectional', $foundOneToOneBidirectionalEntity->getName());
        $this->assertSame($foundProduct, $foundOneToOneBidirectionalEntity->getProduct());

        $foundOneToOneSelfReferencingEntity = $foundProduct->getOneToOneSelfReferencingEntity();
        $this->assertInstanceOf(ExtendedProduct::class, $foundOneToOneSelfReferencingEntity);
        $this->assertSame(self::ONE_TO_ONE_SELF_REFERENCING_PRODUCT_ID, $foundOneToOneSelfReferencingEntity->getId());

        $foundOneToManyBidirectionalEntities = $foundProduct->getOneToManyBidirectionalEntities();
        $this->assertCount(1, $foundOneToManyBidirectionalEntities);
        $foundOneToManyBidirectionalEntity = reset($foundOneToManyBidirectionalEntities);
        $this->assertSame('one-to-many bidirectional', $foundOneToManyBidirectionalEntity->getName());

        $foundOneToManyUnidirectionalWithJoinTableEntities = $foundProduct->getOneToManyUnidirectionalWithJoinTableEntities();
        $this->assertCount(1, $foundOneToManyUnidirectionalWithJoinTableEntities);
        $foundOneToManyUnidirectionalWithJoinTableEntity = reset($foundOneToManyUnidirectionalWithJoinTableEntities);
        $this->assertSame('one-to-many unidirectional with join table', $foundOneToManyUnidirectionalWithJoinTableEntity->getName());

        $foundOneToManySelfReferencingEntities = $foundProduct->getOneToManySelfReferencingEntities();
        $this->assertCount(1, $foundOneToManySelfReferencingEntities);
        $foundOneToManySelfReferencingEntity = reset($foundOneToManySelfReferencingEntities);
        $this->assertInstanceOf(ExtendedProduct::class, $foundOneToManySelfReferencingEntity);
        $this->assertSame(self::ONE_TO_MANY_SELF_REFERENCING_PRODUCT_ID, $foundOneToManySelfReferencingEntity->getId());
        $this->assertSame($foundProduct, $foundOneToManySelfReferencingEntity->getOneToManySelfReferencingInverseEntity());

        $foundManyToManyUnidirectionalEntities = $foundProduct->getManyToManyUnidirectionalEntities();
        $this->assertCount(1, $foundManyToManyUnidirectionalEntities);
        $foundManyToManyUnidirectionalEntity = reset($foundManyToManyUnidirectionalEntities);
        $this->assertInstanceOf(UnidirectionalEntity::class, $foundManyToManyUnidirectionalEntity);
        $this->assertSame('many-to-many unidirectional', $foundManyToManyUnidirectionalEntity->getName());

        $foundManyToManyBidirectionalEntities = $foundProduct->getManyToManyBidirectionalEntities();
        $this->assertCount(1, $foundManyToManyBidirectionalEntities);
        $foundManyToManyBidirectionalEntity = reset($foundManyToManyBidirectionalEntities);
        $this->assertInstanceOf(ProductManyToManyBidirectionalEntity::class, $foundManyToManyBidirectionalEntity);
        $this->assertSame('many-to-many bidirectional', $foundManyToManyBidirectionalEntity->getName());

        $foundManyToManySelfReferencingEntities = $foundProduct->getManyToManySelfReferencingEntities();
        $this->assertCount(1, $foundManyToManySelfReferencingEntities);
        $foundManyToManySelfReferencingEntity = reset($foundManyToManySelfReferencingEntities);
        $this->assertInstanceOf(ExtendedProduct::class, $foundManyToManySelfReferencingEntity);
        $this->assertSame(self::MANY_TO_MANY_SELF_REFERENCING_PRODUCT_ID, $foundManyToManySelfReferencingEntity->getId());
        $foundManyToManySelfReferencingInverseEntities = $foundManyToManySelfReferencingEntity->getManyToManySelfReferencingInverseEntities();
        $this->assertCount(1, $foundManyToManySelfReferencingInverseEntities);
        $foundManyToManySelfReferencingInverseEntity = reset($foundManyToManySelfReferencingInverseEntities);
        $this->assertInstanceOf(ExtendedProduct::class, $foundManyToManySelfReferencingInverseEntity);
        $this->assertSame($foundProduct, $foundManyToManySelfReferencingInverseEntity);
    }

    /**
     * @param int $id
     * @return \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedProduct
     */
    private function getProduct(int $id): ExtendedProduct
    {
        $qb = $this->em->createQueryBuilder();
        $qb->from(ExtendedProduct::class, 'p')
            ->select('p')
            ->where('p.id = :id')
            ->setParameter(':id', $id);
        $query = $qb->getQuery();
        $product = $query->getSingleResult();
        $this->assertInstanceOf(ExtendedProduct::class, $product);
        return $product;
    }

    private function doTestExtendedCategoryPersistence(): void
    {
        $category = $this->getCategory(self::MAIN_CATEGORY_ID);

        $category->setStringField('main category string');

        $manyToOneUnidirectionalEntity = new UnidirectionalEntity('many-to-one unidirectional');
        $this->em->persist($manyToOneUnidirectionalEntity);
        $category->setManyToOneUnidirectionalEntity($manyToOneUnidirectionalEntity);

        $oneToOneUnidirectionalEntity = new UnidirectionalEntity('one-to-one unidirectional');
        $this->em->persist($oneToOneUnidirectionalEntity);
        $category->setOneToOneUnidirectionalEntity($oneToOneUnidirectionalEntity);

        $oneToOneBidirectionalEntity = new CategoryOneToOneBidirectionalEntity('one-to-one bidirectional');
        $this->em->persist($oneToOneBidirectionalEntity);
        $category->setOneToOneBidirectionalEntity($oneToOneBidirectionalEntity);

        $oneToOneSelfReferencingEntity = $this->getCategory(self::ONE_TO_ONE_SELF_REFERENCING_CATEGORY_ID);
        $category->setOneToOneSelfReferencingEntity($oneToOneSelfReferencingEntity);

        $oneToManyBidirectionalEntity = new CategoryOneToManyBidirectionalEntity('one-to-many bidirectional');
        $this->em->persist($oneToManyBidirectionalEntity);
        $category->addOneToManyBidirectionalEntity($oneToManyBidirectionalEntity);

        $oneToManyUnidirectionalWithJoinTableEntity = new UnidirectionalEntity('one-to-many unidirectional with join table');
        $this->em->persist($oneToManyUnidirectionalWithJoinTableEntity);
        $category->addOneToManyUnidirectionalWithJoinTableEntity($oneToManyUnidirectionalWithJoinTableEntity);

        $oneToManySelfReferencingEntity = $this->getCategory(self::ONE_TO_MANY_SELF_REFERENCING_CATEGORY_ID);
        $category->addOneToManySelfReferencingEntity($oneToManySelfReferencingEntity);

        $manyToManyUnidirectionalEntity = new UnidirectionalEntity('many-to-many unidirectional');
        $this->em->persist($manyToManyUnidirectionalEntity);
        $category->addManyToManyUnidirectionalEntity($manyToManyUnidirectionalEntity);

        $manyToManyBidirectionalEntity = new CategoryManyToManyBidirectionalEntity('many-to-many bidirectional');
        $this->em->persist($manyToManyBidirectionalEntity);
        $category->addManyToManyBidirectionalEntity($manyToManyBidirectionalEntity);

        $manyToManySelfReferencingEntity = $this->getCategory(self::MANY_TO_MANY_SELF_REFERENCING_CATEGORY_ID);
        $category->addManyToManySelfReferencingEntity($manyToManySelfReferencingEntity);

        $this->em->flush();
        $this->em->clear();

        $foundCategory = $this->getCategory(self::MAIN_CATEGORY_ID);

        $this->assertSame('main category string', $foundCategory->getStringField());

        $foundManyToOneUnidirectionalEntity = $foundCategory->getManyToOneUnidirectionalEntity();
        $this->assertInstanceOf(UnidirectionalEntity::class, $foundManyToOneUnidirectionalEntity);
        $this->assertSame('many-to-one unidirectional', $foundManyToOneUnidirectionalEntity->getName());

        $foundOneToOneUnidirectionalEntity = $foundCategory->getOneToOneUnidirectionalEntity();
        $this->assertInstanceOf(UnidirectionalEntity::class, $foundOneToOneUnidirectionalEntity);
        $this->assertSame('one-to-one unidirectional', $foundOneToOneUnidirectionalEntity->getName());

        $foundOneToOneBidirectionalEntity = $foundCategory->getOneToOneBidirectionalEntity();
        $this->assertInstanceOf(CategoryOneToOneBidirectionalEntity::class, $foundOneToOneBidirectionalEntity);
        $this->assertSame('one-to-one bidirectional', $foundOneToOneBidirectionalEntity->getName());
        $this->assertSame($foundCategory, $foundOneToOneBidirectionalEntity->getCategory());

        $foundOneToOneSelfReferencingEntity = $foundCategory->getOneToOneSelfReferencingEntity();
        $this->assertInstanceOf(ExtendedCategory::class, $foundOneToOneSelfReferencingEntity);
        $this->assertSame(self::ONE_TO_ONE_SELF_REFERENCING_CATEGORY_ID, $foundOneToOneSelfReferencingEntity->getId());

        $foundOneToManyBidirectionalEntities = $foundCategory->getOneToManyBidirectionalEntities();
        $this->assertCount(1, $foundOneToManyBidirectionalEntities);
        $foundOneToManyBidirectionalEntity = reset($foundOneToManyBidirectionalEntities);
        $this->assertSame('one-to-many bidirectional', $foundOneToManyBidirectionalEntity->getName());

        $foundOneToManyUnidirectionalWithJoinTableEntities = $foundCategory->getOneToManyUnidirectionalWithJoinTableEntities();
        $this->assertCount(1, $foundOneToManyUnidirectionalWithJoinTableEntities);
        $foundOneToManyUnidirectionalWithJoinTableEntity = reset($foundOneToManyUnidirectionalWithJoinTableEntities);
        $this->assertSame('one-to-many unidirectional with join table', $foundOneToManyUnidirectionalWithJoinTableEntity->getName());

        $foundOneToManySelfReferencingEntities = $foundCategory->getOneToManySelfReferencingEntities();
        $this->assertCount(1, $foundOneToManySelfReferencingEntities);
        $foundOneToManySelfReferencingEntity = reset($foundOneToManySelfReferencingEntities);
        $this->assertInstanceOf(ExtendedCategory::class, $foundOneToManySelfReferencingEntity);
        $this->assertSame(self::ONE_TO_MANY_SELF_REFERENCING_CATEGORY_ID, $foundOneToManySelfReferencingEntity->getId());
        $this->assertSame($foundCategory, $foundOneToManySelfReferencingEntity->getOneToManySelfReferencingInverseEntity());

        $foundManyToManyUnidirectionalEntities = $foundCategory->getManyToManyUnidirectionalEntities();
        $this->assertCount(1, $foundManyToManyUnidirectionalEntities);
        $foundManyToManyUnidirectionalEntity = reset($foundManyToManyUnidirectionalEntities);
        $this->assertInstanceOf(UnidirectionalEntity::class, $foundManyToManyUnidirectionalEntity);
        $this->assertSame('many-to-many unidirectional', $foundManyToManyUnidirectionalEntity->getName());

        $foundManyToManyBidirectionalEntities = $foundCategory->getManyToManyBidirectionalEntities();
        $this->assertCount(1, $foundManyToManyBidirectionalEntities);
        $foundManyToManyBidirectionalEntity = reset($foundManyToManyBidirectionalEntities);
        $this->assertInstanceOf(CategoryManyToManyBidirectionalEntity::class, $foundManyToManyBidirectionalEntity);
        $this->assertSame('many-to-many bidirectional', $foundManyToManyBidirectionalEntity->getName());

        $foundManyToManySelfReferencingEntities = $foundCategory->getManyToManySelfReferencingEntities();
        $this->assertCount(1, $foundManyToManySelfReferencingEntities);
        $foundManyToManySelfReferencingEntity = reset($foundManyToManySelfReferencingEntities);
        $this->assertInstanceOf(ExtendedCategory::class, $foundManyToManySelfReferencingEntity);
        $this->assertSame(self::MANY_TO_MANY_SELF_REFERENCING_CATEGORY_ID, $foundManyToManySelfReferencingEntity->getId());
        $foundManyToManySelfReferencingInverseEntities = $foundManyToManySelfReferencingEntity->getManyToManySelfReferencingInverseEntities();
        $this->assertCount(1, $foundManyToManySelfReferencingInverseEntities);
        $foundManyToManySelfReferencingInverseEntity = reset($foundManyToManySelfReferencingInverseEntities);
        $this->assertInstanceOf(ExtendedCategory::class, $foundManyToManySelfReferencingInverseEntity);
        $this->assertSame($foundCategory, $foundManyToManySelfReferencingInverseEntity);
    }

    /**
     * @param int $id
     * @return \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedCategory
     */
    public function getCategory(int $id): ExtendedCategory
    {
        $qb = $this->em->createQueryBuilder();
        $qb->from(ExtendedCategory::class, 'c')
            ->select('c')
            ->where('c.id = :id')
            ->setParameter(':id', $id);
        $query = $qb->getQuery();
        $category = $query->getSingleResult();
        $this->assertInstanceOf(ExtendedCategory::class, $category);
        return $category;
    }

    private function doTestExtendedOrderItemsPersistence(): void
    {
        $orderItem = $this->getOrderItem(self::ORDER_ITEM_ID);
        $orderItem->setStringField('string value');

        $this->em->flush();
        $this->em->clear();

        $foundItem = $this->getOrderItem(self::ORDER_ITEM_ID);
        $this->assertSame('string value', $foundItem->getStringField());
    }

    /**
     * @param int $id
     * @return \Tests\ShopBundle\Functional\EntityExtension\Model\ExtendedOrderItem
     */
    private function getOrderItem(int $id): ExtendedOrderItem
    {
        $qb = $this->em->createQueryBuilder();
        $qb->from(ExtendedOrderItem::class, 'i')
            ->select('i')
            ->where('i.id = :id')
            ->setParameter(':id', $id);
        $query = $qb->getQuery();
        $result = $query->getSingleResult();
        $this->assertInstanceOf(ExtendedOrderItem::class, $result);
        return $result;
    }

    /**
     * @param string $parentEntityName
     * @param string $extendedEntityName
     * @param int $entityId
     */
    private function doTestExtendedEntityInstantiation(
        string $parentEntityName,
        string $extendedEntityName,
        int $entityId
    ): void {
        $repository = $this->em->getRepository($parentEntityName);
        $this->assertInstanceOf($extendedEntityName, $repository->find($entityId));

        $query = $this->em->createQuery('SELECT x FROM ' . $parentEntityName . ' x WHERE x.id = :id')
            ->setParameter('id', $entityId);
        $this->assertInstanceOf($extendedEntityName, $query->getSingleResult());

        $qb = $this->em->createQueryBuilder();
        $qb->from($parentEntityName, 'x')
            ->select('x')
            ->where('x.id = :id')
            ->setParameter(':id', $entityId);
        $this->assertInstanceOf($extendedEntityName, $qb->getQuery()->getSingleResult());

        $entity = $this->em->find($parentEntityName, $entityId);
        $this->assertInstanceOf($extendedEntityName, $entity);
    }
}
