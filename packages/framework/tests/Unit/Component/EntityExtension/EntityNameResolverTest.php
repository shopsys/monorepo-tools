<?php

namespace Tests\FrameworkBundle\Unit\Component\EntityExtension;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use stdClass;

class EntityNameResolverTest extends TestCase
{
    const PARENT_ENTITY_FQCN = 'Vendor\Bundle\Folder\ParentEntityName';
    const CUSTOM_ENTITY_FQCN = 'MyBundle\MyFolder\MyEntityName';
    const STRING_WITH_SPACES_ON_BORDERS = ' string ';
    const STRING_WITHOUT_SPACES_ON_BORDERS = 'string';

    public function resolvingProvider(): array
    {
        return [
            'empty map' => [
                'map' => [],
                'value' => 'Shopsys\FrameworkBundle\Model\Entity',
                'expected' => 'Shopsys\FrameworkBundle\Model\Entity',
            ],
            'replacing exact match' => [
                'map' => ['Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity'],
                'value' => 'Shopsys\FrameworkBundle\Model\Entity',
                'expected' => 'Shopsys\ShopBundle\Model\MyEntity',
            ],
            'not replacing other entity name (that is not in map)' => [
                'map' => ['Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity'],
                'value' => 'Shopsys\FrameworkBundle\Model\OtherEntity',
                'expected' => 'Shopsys\FrameworkBundle\Model\OtherEntity',
            ],
            'not replacing partially matching entity name' => [
                'map' => ['Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity'],
                'value' => 'Shopsys\FrameworkBundle\Model\Entity\Item',
                'expected' => 'Shopsys\FrameworkBundle\Model\Entity\Item',
            ],
            'not replacing in DQL' => [
                'map' => ['Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity'],
                'value' => 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity',
                'expected' => 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity',
            ],
            'replacing exact match in multiple-item map' => [
                'map' => [
                    'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
                    'Shopsys\FrameworkBundle\Model\OtherEntity' => 'Shopsys\ShopBundle\Model\MyOtherEntity',
                ],
                'value' => 'Shopsys\FrameworkBundle\Model\OtherEntity',
                'expected' => 'Shopsys\ShopBundle\Model\MyOtherEntity',
            ],
        ];
    }

    /**
     * @dataProvider resolvingProvider
     */
    public function testResolving(array $map, string $value, string $expected): void
    {
        $entityNameResolver = new EntityNameResolver($map);
        $resolvedValue = $entityNameResolver->resolve($value);
        $this->assertSame($expected, $resolvedValue);
    }

    public function resolvingInStringsProvider(): array
    {
        $resolveDataProvider = $this->resolvingProvider();

        unset($resolveDataProvider['not replacing in DQL']);

        return $resolveDataProvider + [
                'replacing in DQL' => [
                    'map' => ['Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity'],
                    'value' => 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity',
                    'expected' => 'SELECT * FROM Shopsys\ShopBundle\Model\MyEntity',
                ],
                'replacing multiple occurrences of the same entity name' => [
                    'map' => ['Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity'],
                    'value' => 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity JOIN Shopsys\FrameworkBundle\Model\Entity',
                    'expected' => 'SELECT * FROM Shopsys\ShopBundle\Model\MyEntity JOIN Shopsys\ShopBundle\Model\MyEntity',
                ],
                'replacing multiple entity names' => [
                    'map' => [
                        'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
                        'Shopsys\FrameworkBundle\Model\OtherEntity' => 'Shopsys\ShopBundle\Model\MyOtherEntity',
                    ],
                    'value' => 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity JOIN Shopsys\FrameworkBundle\Model\OtherEntity',
                    'expected' => 'SELECT * FROM Shopsys\ShopBundle\Model\MyEntity JOIN Shopsys\ShopBundle\Model\MyOtherEntity',
                ],
            ];
    }

    /**
     * @dataProvider resolvingInStringsProvider
     */
    public function testResolvingInStrings(array $map, string $value, string $expected): void
    {
        $entityNameResolver = new EntityNameResolver($map);
        $resolvedValue = $entityNameResolver->resolveIn($value);
        $this->assertSame($expected, $resolvedValue);
    }

    public function testResolvingInNull(): void
    {
        $entityNameResolver = new EntityNameResolver([]);
        $resolvedValue = $entityNameResolver->resolveIn(null);
        $this->assertNull($resolvedValue);
    }

    public function testResolvingInArray(): void
    {
        $entityNameResolver = new EntityNameResolver([
            'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
            'Shopsys\FrameworkBundle\Model\OtherEntity' => 'Shopsys\ShopBundle\Model\MyOtherEntity',
        ]);

        $resolvedValue = $entityNameResolver->resolveIn([
            'in DQL' => 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity',
            'multiple entity names' => 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity JOIN Shopsys\FrameworkBundle\Model\OtherEntity',
            'recursive' => [
                'Shopsys\FrameworkBundle\Model\Entity',
                'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity',
                'Shopsys\FrameworkBundle\Model\Entity\NonExtendedItem',
            ],
        ]);

        $this->assertSame([
            'in DQL' => 'SELECT * FROM Shopsys\ShopBundle\Model\MyEntity',
            'multiple entity names' => 'SELECT * FROM Shopsys\ShopBundle\Model\MyEntity JOIN Shopsys\ShopBundle\Model\MyOtherEntity',
            'recursive' => [
                'Shopsys\ShopBundle\Model\MyEntity',
                'SELECT * FROM Shopsys\ShopBundle\Model\MyEntity',
                'Shopsys\FrameworkBundle\Model\Entity\NonExtendedItem',
            ],
        ], $resolvedValue);
    }

    public function testResolvingInPropertiesReturnsSameObject(): void
    {
        $entityNameResolver = new EntityNameResolver([
            'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
        ]);
        $object = new stdClass();
        $object->property = 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity';

        $resolvedObject = $entityNameResolver->resolveIn($object);

        $this->assertSame($object, $resolvedObject);
    }

    public function testResolvingInPublicProperty(): void
    {
        $entityNameResolver = new EntityNameResolver([
            'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
        ]);
        $object = new stdClass();
        $object->property = 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity';

        $entityNameResolver->resolveIn($object);

        $this->assertSame('SELECT * FROM Shopsys\ShopBundle\Model\MyEntity', $object->property);
    }

    public function testResolvingInPrivateProperty(): void
    {
        $entityNameResolver = new EntityNameResolver([
            'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
        ]);
        $object = new class() {
            private $property = 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity';

            public function getProperty(): string
            {
                return $this->property;
            }
        };

        $entityNameResolver->resolveIn($object);

        $this->assertSame('SELECT * FROM Shopsys\ShopBundle\Model\MyEntity', $object->getProperty());
    }

    public function testResolvingInPropertiesRecursively(): void
    {
        $entityNameResolver = new EntityNameResolver([
            'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
        ]);
        $innerObject = new class() {
            private $property = 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity';

            public function getProperty(): string
            {
                return $this->property;
            }
        };
        $object = new stdClass();
        $object->property = $innerObject;

        $entityNameResolver->resolveIn($object);

        $this->assertSame('SELECT * FROM Shopsys\ShopBundle\Model\MyEntity', $object->property->getProperty());
    }

    public function testResolvingInMultipleProperties(): void
    {
        $entityNameResolver = new EntityNameResolver([
            'Shopsys\FrameworkBundle\Model\Entity' => 'Shopsys\ShopBundle\Model\MyEntity',
        ]);
        $object = new stdClass();
        $object->dql = 'SELECT * FROM Shopsys\FrameworkBundle\Model\Entity';
        $object->otherEntity = 'Shopsys\FrameworkBundle\Model\OtherEntity';
        $object->array = [
            'Shopsys\FrameworkBundle\Model\Entity',
            'Shopsys\FrameworkBundle\Model\Entity\NonExtendedItem',
        ];

        $entityNameResolver->resolveIn($object);

        $this->assertSame('SELECT * FROM Shopsys\ShopBundle\Model\MyEntity', $object->dql);
        $this->assertSame('Shopsys\FrameworkBundle\Model\OtherEntity', $object->otherEntity);
        $this->assertSame([
            'Shopsys\ShopBundle\Model\MyEntity',
            'Shopsys\FrameworkBundle\Model\Entity\NonExtendedItem',
        ], $object->array);
    }
}
