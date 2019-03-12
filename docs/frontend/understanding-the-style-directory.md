# Understanding the style directory
In folder `src/Shopsys/ShopBundle/Resources/styles/front/` there exists folders containing styles for individual domains.

For each domain exists the same folder structure consists of some folders and files. This document is about describing the meaning of these files and folders.

## Terms definition
We are using a few terms for our coding standards in Shopsy Framework. In order to understand our folder hierarchy is good to know their meaning.

### Helper
Helper is class containing definition of a single attribute. In connection with `LESS preprocesor` helper classes may be very powerful.

Let's show you some code of helper class. You can see there is a definition of the helper class setting HTML element color to primary web color.
```css
.color-primary {
    color: @color-primary;
}
```

### Component
In Shopsys Framework we use few types of components - `box`, `form`, `in`, `list`, `table`, `window`, `wrap`.

All components are placed in the folder `components`. Each component type has own folder. There exists a rule that component folder name match with the component type name. According to a component type, we will move LESS component file into appropriate folder.

For example component `list-products` will have following file path `components/list/products.less`.

## Root directory
In the root directory for common styles `src/Shopsys/ShopBundle/Resources/styles/front/common` you will see following files and folders.
- `main.less` - import all `Less` files needed to style its domain design. For more information about importing `Less` files, please see our document [Introduction to less](./introduction-to-less.md)
- `helpers.less` - imports base configuration located in `core/` and helper classes located in files placed in `helpers/`
- `todo.less` - serves as file to keep temporary CSS/LESS code for further processing
- `wysiwyg.less` - import files needed to design wysiwyg editor
- `components/` - contains all components
    - `box/` - contains components that are related to block of complex information (product information, progress bar in order process, promo code,...). **This type of component is the only one which is not reusable.**
    - `form/` - contains components related to form elements, wrappers and structures (form line, error messages,...)
    - `in/` - contains inline components (product flag, messages, paging,...). Here belong all components that are not related to complex of information.
    - `list/` - contains components related to list that are not created by HTML element table (product list, menu, categories,...). Here belong all components that are a list of items and are not created by HTML element table.
    - `table/` - contains components related to table HTML element (product parameters, cart,...). Here belong all components that are a list of items and created by HTML element table.
    - `window/` - contains components for showing window (popup)
    - `wrap/` - contains components that wrap elements
- `core/` - contains core setting
    - `form/` - contains styles related to form elements
        - `btn.less` - definition of `.btn` class and its modifications
        - `input.less` - definition of base input HTLM element, `.input` class and its modifications
    - `mixin/` - contains usefull `Less mixins`
    - `animation.less` - used for CSS animation
    - `base.less` - set base styles for HTML elements
    - `reset.less` - reset CSS attributes of HTML elements
    - `typography.less` - set base styles for typography
    - `variables.less` - contains variables definition
- `helpers/` - contains helper classes for setting `display`, `font-size`, `cursor`, `float`, `text-align`, `clear`, `margin`, `padding` attributes
- `layout/` - contains styles related to web layout
    - `header/` - contains all classes related to `.header` class
- `libs/` - contains styles of third side application
- `print/` - contains styles intended for print page. Structure of folder `print/` copy folder structure of this root folder. You will find in `print/` only files that were needed to be modified in order to show print page correctly.

## How to create new LESS component
First of all, it is good to select a class name that will fit the purpose of the `LESS component`.

After that you have to realize which type of component it should be. To determine where a new component belongs, it is recommended to follow the suggestion mentioned in the previous section **Root directory**. You can find these suggestions in the description for individual component folders.

## How to style print page
For this purpose please access `print/` folder. In the folder place only files that are necessary to style print page. It is recommended to respect folder structure as is in the root folder.

### Best practices
In case you want to hide HTML element there exist CSS class `.dont-print`. It is best practice not to hide element through styles but to place this class to an appropriate element.

### Example 1 - Setting green color on title in product list
In order to style the product list, you will have to find an appropriate `LESS component`. In our case it would be placed in `src/Shopsys/ShopBundle/Resources/styles/front/common/print/components/list/products.less`. It is also recommended to respect the way of writing a class name and its definition. We use BEM methodology in Shopsys Framework (for more information please visit [Introduction to BEM](http://getbem.com/introduction/)).

Let's assume we need to set a green color for product title in the product list.

The code in file `print/components/list/products.less` would look like as it is shown below.
```less
.list-products {
    &__item {
        &__title {
            color: green;
        }
    }
}
```

### Example 2 - Hidding all advert places
In order to hide all advert places, we will proceed as recommended within best practices part.

File `src/Shopsys/ShopBundle/Resources/views/Front/Content/Advert/box.html.twig` would look like is shown below.
```twig
{% if advert %}
    <div class="in-place in-place--{{advert.positionName}} dont-print">
        {% if advert.type == 'image' %}
            {% if advert.link == null %}
                {{ image(advert, { size: 'original' }) }}
            {% else %}
                <a href="{{ advert.link }}">{{ image(advert, { size: 'original' }) }}</a>
            {% endif %}
        {% elseif advert.type == 'code' %}
            {{ advert.code | raw }}
        {% endif %}
    </div>
{% endif %}
```
