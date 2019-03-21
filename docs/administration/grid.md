# Grid

## Basics
Grid is a component that displays data in a customizable table view in the administration.
It helps to present data to the user, can paginate the results, allows ordering by some column, allows setting row priority with row rearranging, supports actions on the row level and mass actions in general.

## Features
* Pagination
* Sorting by columns
* Drag&Drop (Sortable)
* Multiple Drag&Drop (Sortable across multiple grid instances)
* Actions (e.g. deletion of an entity in a row)
* Bulk Actions
* Inline editing
* Extendable template
* Support for multiple data sources

## Philosophy
Each grid should be created with its own factory in which a whole grid is configured.
This factory can use `GridFactory` class from Shopsys Framework to create a basic grid object, which will be adjusted.

Grid uses object implementing `DataSourceInterface` to obtain data to be rendered.
Read more about various data sources in the [Grid data sources](/docs/administration/grid-data-sources.md) article.

## Configurations
- **Display only**
    - When you just need to display some data to the user.
    - e.g. `Marketing > XML Feeds`
- **Display with action**
    - When you need to perform some actions on the grid entries, such as deletion.
    - e.g. `Marketing > E-mail newsletter`
- **Inline editable**
    - When the entities in the grid are simple enough and do not need a separate page for editing, you can edit them directly in the grid via AJAX.
    - e.g. `Pricing > Promo codes`
- **Drag&Drop**
    - When you need to set some ordering of your entities manually.
    - e.g.`Marketing > Slider pages`
- **Multiple Drag&Drop**
    - When you need to set some ordering of your entities manually among multiple sections.
    - e.g. `Marketing > Articles overview`
- **Selectable**
    - when you need to select entries from the grid and apply some bulk actions on them.
    - e.g. `Products > Products overview`

## Customization of grid rendering
It is really easy to customize the appearance of your grid by overriding suitable Twig blocks of the default Grid template.
Read the [separate article](/docs/administration/grid-rendering-customization.md) to get more information.

## Extending an existing grid
Usually, the grids are created in their factories (e.g. `\Shopsys\FrameworkBundle\Model\Payment\Grid\PaymentGridFactory`)
in `create()` method. If you need to add a new column, in most cases it should be sufficient to override the method:
```php
namespace Shopsys\ShopBundle\Grid\Payment;

use Shopsys\FrameworkBundle\Model\Payment\Grid\PaymentGridFactory as BasePaymentGridFactory;

class PaymentGridFactory extends BasePaymentGridFactory
{
    public function create()
    {
        $grid = parent::create();
        $grid->addColumn('myNewAttribute', 'p.myNewAttribute', t('My new attribute label'));

        return $grid;
    }
}
```
and then set your class as an alias for the original one in `services.yml` configuration file:
```yaml
Shopsys\FrameworkBundle\Model\Payment\Grid\PaymentGridFactory: '@Shopsys\ShopBundle\Grid\PaymentGridFactory'
```

However, not all grids have their own factories, sometimes they are created in protected method of a controller, and sometimes you may need to perform some more advanced customizations (e.g. change the data source for the grid).
In such a case, you need to fork the corresponding method and rewrite it to suit your needs.
We are planning some refactorings to enable you easier and unified way of grid customizations.

## Further reading - cookbooks
If you want to implement a new grid, you can follow the cookbooks:
- [Create basic grid](/docs/cookbook/create-basic-grid.md)
- [Create advanced grid](/docs/cookbook/create-advanced-grid.md)
