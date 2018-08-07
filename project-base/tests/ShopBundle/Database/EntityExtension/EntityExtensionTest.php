<?php

namespace Tests\ShopBundle\Database\EntityExtension;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderPayment;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderTransport;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Tests\ShopBundle\Database\EntityExtension\Model\CategoryManyToManyBidirectionalEntity;
use Tests\ShopBundle\Database\EntityExtension\Model\CategoryOneToManyBidirectionalEntity;
use Tests\ShopBundle\Database\EntityExtension\Model\CategoryOneToOneBidirectionalEntity;
use Tests\ShopBundle\Database\EntityExtension\Model\ExtendedCategory;
use Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderItem;
use Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderPayment;
use Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderProduct;
use Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderTransport;
use Tests\ShopBundle\Database\EntityExtension\Model\ExtendedProduct;
use Tests\ShopBundle\Database\EntityExtension\Model\ProductManyToManyBidirectionalEntity;
use Tests\ShopBundle\Database\EntityExtension\Model\ProductOneToManyBidirectionalEntity;
use Tests\ShopBundle\Database\EntityExtension\Model\ProductOneToOneBidirectionalEntity;
use Tests\ShopBundle\Database\EntityExtension\Model\UnidirectionalEntity;
use Tests\ShopBundle\Test\DatabaseTestCase;

class EntityExtensionTest extends DatabaseTestCase
{
    const MAIN_PRODUCT_ID = 1;
    const ONE_TO_ONE_SELF_REFERENCING_PRODUCT_ID = 2;
    const ONE_TO_MANY_SELF_REFERENCING_PRODUCT_ID = 3;
    const MANY_TO_MANY_SELF_REFERENCING_PRODUCT_ID = 4;

    const MAIN_CATEGORY_ID = 1;
    const ONE_TO_ONE_SELF_REFERENCING_CATEGORY_ID = 2;
    const ONE_TO_MANY_SELF_REFERENCING_CATEGORY_ID = 3;
    const MANY_TO_MANY_SELF_REFERENCING_CATEGORY_ID = 4;

    const ORDER_TRANSPORT_ID = 1;
    const ORDER_PAYMENT_ID = 2;
    const ORDER_PRODUCT_ID = 3;

    /**
     * @var \Doctrine\ORM\EntityManager
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
            OrderProduct::class => ExtendedOrderProduct::class,
            OrderPayment::class => ExtendedOrderPayment::class,
            OrderTransport::class => ExtendedOrderTransport::class,
        ];

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
            $metadataDriverChain->addDriver($driver, 'Tests\\ShopBundle\\Database\\EntityExtension');
        } else {
            $this->fail(sprintf('Metadata driver must be type of %s', MappingDriverChain::class));
        }
    }

    /**
     * @param string[] $entityExtensionMap
     */
    public function overwriteEntityExtensionMapInServicesInContainer(array $entityExtensionMap): void
    {
        $loadORMMetadataSubscriber = $this->getContainer()->get('joschi127_doctrine_entity_override.event_subscriber.load_orm_metadata');
        /* @var $loadORMMetadataSubscriber \Tests\ShopBundle\Database\EntityExtension\OverwritableLoadORMMetadataSubscriber */
        $entityNameResolver = $this->getContainer()->get(EntityNameResolver::class);
        /* @var $entityNameResolver \Tests\ShopBundle\Database\EntityExtension\OverwritableEntityNameResolver */

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
        $this->doTestExtendedEntityInstantiation(OrderItem::class, ExtendedOrderPayment::class, self::ORDER_PAYMENT_ID);
        $this->doTestExtendedEntityInstantiation(OrderItem::class, ExtendedOrderProduct::class, self::ORDER_PRODUCT_ID);
        $this->doTestExtendedEntityInstantiation(OrderItem::class, ExtendedOrderTransport::class, self::ORDER_TRANSPORT_ID);
        $this->doTestExtendedEntityInstantiation(OrderPayment::class, ExtendedOrderPayment::class, self::ORDER_PAYMENT_ID);
        $this->doTestExtendedEntityInstantiation(OrderProduct::class, ExtendedOrderProduct::class, self::ORDER_PRODUCT_ID);
        $this->doTestExtendedEntityInstantiation(OrderTransport::class, ExtendedOrderTransport::class, self::ORDER_TRANSPORT_ID);
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
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedProduct
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
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedCategory
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
        $orderTransport = $this->getOrderTransport(self::ORDER_TRANSPORT_ID);
        $orderTransport->setStringField('string for order transport');
        $orderTransport->setTransportStringField('specific string for order transport');

        $orderPayment = $this->getOrderPayment(self::ORDER_PAYMENT_ID);
        $orderPayment->setStringField('string for order payment');
        $orderPayment->setPaymentStringField('specific string for order payment');

        $orderProduct = $this->getOrderProduct(self::ORDER_PRODUCT_ID);
        $orderProduct->setStringField('string for order product');
        $orderProduct->setProductStringField('specific string for order product');

        $this->em->flush();
        $this->em->clear();

        $foundTransport = $this->getOrderTransport(self::ORDER_TRANSPORT_ID);
        $this->assertSame('string for order transport', $foundTransport->getStringField());
        $this->assertSame('specific string for order transport', $foundTransport->getTransportStringField());
        $this->assertInstanceOf(Transport::class, $foundTransport->getTransport());

        $foundPayment = $this->getOrderPayment(self::ORDER_PAYMENT_ID);
        $this->assertSame('string for order payment', $foundPayment->getStringField());
        $this->assertSame('specific string for order payment', $foundPayment->getPaymentStringField());
        $this->assertInstanceOf(Payment::class, $foundPayment->getPayment());

        $foundProduct = $this->getOrderProduct(self::ORDER_PRODUCT_ID);
        $this->assertSame('string for order product', $foundProduct->getStringField());
        $this->assertSame('specific string for order product', $foundProduct->getProductStringField());
        $this->assertInstanceOf(ExtendedProduct::class, $foundProduct->getProduct());
    }

    /**
     * @param int $id
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedCategory
     */
    private function getOrderTransport(int $id): ExtendedOrderTransport
    {
        $orderItem = $this->getOrderItem($id);
        $this->assertInstanceOf(ExtendedOrderTransport::class, $orderItem);
        return $orderItem;
    }

    /**
     * @param int $id
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderPayment
     */
    private function getOrderPayment(int $id): ExtendedOrderPayment
    {
        $orderItem = $this->getOrderItem($id);
        $this->assertInstanceOf(ExtendedOrderPayment::class, $orderItem);
        return $orderItem;
    }

    /**
     * @param int $id
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderProduct
     */
    private function getOrderProduct(int $id): ExtendedOrderProduct
    {
        $orderItem = $this->getOrderItem($id);
        $this->assertInstanceOf(ExtendedOrderProduct::class, $orderItem);
        return $orderItem;
    }

    /**
     * @param int $id
     * @return \Tests\ShopBundle\Database\EntityExtension\Model\ExtendedOrderItem
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
