# Grid Data Sources

As a data source, the [Grid Component](/docs/administration/grid.md) requires an implementation of [`DataSourceInterface`](/packages/framework/src/Component/Grid/DataSourceInterface.php).

You can find 4 implementations of the interface in Shopsys Framework.

## [`QueryBuilderDataSource`](/packages/framework/src/Component/Grid/QueryBuilderDataSource.php)
Most commonly used data source that is created from Doctrine Query Builder.
### Example of usage
```php
/** @var Doctrine\ORM\EntityManagerInterface $entityManager */

$queryBuilder = $entityManager->createQueryBuilder();

$queryBuilder->select('p')
    ->from(Product::class, 'p');

$dataSource = new QueryBuilderDataSource($queryBuilder, 'p.id');
```

## [`QueryBuilderWithRowManipulatorDataSource`](/packages/framework/src/Component/Grid/QueryBuilderWithRowManipulatorDataSource.php)
This data source is created from query builder as well, and on top of it, it allows to define a callback that is applied on each row so additional data can be set this way,
e.g. you can add some calculated price into the data set.
### Example of usage
```php
/** @var Shopsys\FrameworkBundle\Model\Transport\TransportRepository $transportRepository */
/** @var Shopsys\FrameworkBundle\Model\Localization\Localization $localization */

$queryBuilder = $transportRepository->getQueryBuilderForAll()
    ->addSelect('tt')
    ->join('t.translations', 'tt', Join::WITH, 'tt.locale = :locale')
    ->setParameter('locale', $localization->getAdminLocale());

$dataSource = new QueryBuilderWithRowManipulatorDataSource(
    $queryBuilder,
    't.id',
    function ($row) {
        $transport = $transportRepository->findById($row['t']['id']);
        $row['displayPrice'] = getDisplayPrice($transport);
        return $row;
    }
);
```

## [`ArrayDataSource`](/packages/framework/src/Component/Grid/ArrayDataSource.php)
Data source that is created from an array. It is suitable when you need to display data that are not stored in the database.
### Example of usage
```php
/** @var Shopsys\FrameworkBundle\Component\Domain\Domain $domain */

$domainData = [];
foreach ($domain->getAll() as $domainConfig) {
    $domainData[] = [
        'id' => $domainConfig->getId(),
        'name' => $domainConfig->getName(),
        'locale' => $domainConfig->getLocale(),
        'icon' => null,
    ];
}

$dataSource = new ArrayDataSource($domainData, 'id');
```
## [`MoneyConvertingDataSourceDecorator`](/packages/framework/src/Component/Grid/MoneyConvertingDataSourceDecorator.php)
A decorator that can be applied to any of the data sources described above. It provides conversion of monetary values in a data set to [`Money` value object](/docs/introduction/how-to-work-with-money.md#money-class).

### Example of usage
```php
$innerDataSource = new QueryBuilderDataSource($queryBuilder, 'u.id');

$dataSource = new MoneyConvertingDataSourceDecorator($innerDataSource, ['ordersSumPrice']);
```
