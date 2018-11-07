# Administration Menu

The admin side menu is implemented by [KnpMenuBundle](https://symfony.com/doc/master/bundles/KnpMenuBundle/index.html) and to extend it, you can use [events](https://symfony.com/doc/master/bundles/KnpMenuBundle/events.html).
An example of such an extension is described in the cookbook [Adding a New Administration Page](/docs/cookbook/adding-a-new-administration-page.md).

To see how the side menu works programmatically, you can see the [`SideMenuBuilder`](/packages/framework/src/Model/AdminNavigation/SideMenuBuilder.php) class where it is created.
The side menu builder is tagged with `knp_menu.menu_builder` and is accessible under the alias `admin_side_menu`.

There are a few customizations on top of the standard [KnpMenu](https://symfony.com/doc/master/bundles/KnpMenuBundle/index.html):

## Template

The template is not configured globally (via `app/config/packages/twig.yml`) but instead the template is provided during the rendering in a Twig template:
```twig
{{ knp_menu_render('admin_side_menu', {template: 'ShopsysFrameworkBundle:Admin/Menu:side_menu.html.twig'}) }}`.
```

The menu template works similarly to the default `knp_menu.html.twig` but it uses BEM classes and supports a few custom features.

## Javascript

The behavior of the side menu is controlled via the JS component [`Shopsys.sideMenu`](/packages/framework/src/Resources/scripts/admin/sideMenu.js).

## Icons

There is an extra attribute `icon` supported to allow icons in the menu.
Currently, it's used only for the first level of menu items but it will work when set to nested items as well.

It can be assigned to menu item in an event subscriber by calling eg. `$menuItem->setExtra('icon', 'cart');`.

A list of all supported icons can be found in `docs/generated/webfont-admin-svg.html` in your project (input the name without the `svg-` prefix).

## Superadmin access

There is an extra boolean attribute `superadmin` supported to allow highlighting of restricted access of the menu.

It's only used for the visual effect, the restriction itself has to be done manually using the method `AuthorizationCheckerInterface::isGranted(Roles::ROLE_SUPER_ADMIN)`.

## Events

After building of the whole menu a `ConfigureMenuEvent::SIDE_MENU_ROOT` event is dispatched.
After adding each of the submenus, a different event is dispatched.
You can take a look at the class [`ConfigureMenuEvent`](/packages/framework/src/Model/AdminNavigation/ConfigureMenuEvent.php) to see all the events and their internals.

These can be used to [extend the menu](https://symfony.com/doc/master/bundles/KnpMenuBundle/events.html), either from the project repository or from modules.

## Routing extension

To render the admin breadcrumb navigation, the menu items are used as well.
For this reason, even pages that are not displayed in the menu are added to it (with a `display` attribute set to `false`).

These items can have a configured route without some mandatory parameters filled in (eg. a product edit page without a product ID parameter).
To avoid throwing the `MissingMandatoryParametersException`, we have replaced original `RoutingExtension` with [our own implementation](/packages/framework/src/Model/AdminNavigation/RoutingExtension.php).
It simply doesn't generate a URI when the exception is thrown.
The route is still used to resolve the current menu item.

## Breadcrumb overriding

To override the last breadcrumb item, a `BreadcrumbOverrider::overrideLastItem(string $label)` call can be used in a controller.
This can be used, for example, to specify which product you're editing in the breadcrumb navigation.
