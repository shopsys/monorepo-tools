# Grid Rendering Customization

Default [Twig](https://twig.symfony.com/) template for rendering of each grid can be found in [`src/Resources/views/Admin/Grid/Grid.html.twig`](/packages/framework/src/Resources/views/Admin/Grid/Grid.html.twig).
The template is composed of a set of Twig blocks and you are able to override any of them when there is a need for customization of the default appearance.

To customize your grid, you just need to create a new template extending the original one, override appropriate blocks and then set the template as a theme of your grid using `Grid::setTheme` method.

## Blocks that are being overridden at most
- `grid_value_cell_id_<column_id>`
    - `<column_id>` stands for the ID of the column that is defined during the grid creation by the first argument of `Grid::addColumn` method
    - used when you need to change the appearance of values in a particular column
    - the original value is available as `value` variable
- `grid_no_data`
     - the block contains a message that is displayed when there are no data in the grid
     - the default (translatable) value is "No records found"

## Example
Let's say we have a grid of salesmen (in fact, such a grid is created in ["Create basic grid"](/docs/cookbook/create-basic-grid.md) cookbook)
and we want to display all their names bold, and also, we want to be more specific when there are no salesmen in our database.

1. Create a new template that extends to original one and override the blocks you need:
    ```twig
    {# src/Shopsys/ShopBundle/Resources/views/Admin/Content/Salesman/listGrid.html.twig #}
    {% extends '@ShopsysFramework/Admin/Grid/Grid.html.twig' %}

    {% block grid_no_data %}
        {{ 'There are no salesmen in your database.' }}
    {% endblock %}

    {% block grid_value_cell_id_name %}
        <strong>{{ value }}</strong>
    {% endblock %}
    ```

2. Set the new theme for your grid:
    ```php
    $grid->setTheme('@ShopsysShop/Admin/Content/Salesman/listGrid.html.twig');
    ```
