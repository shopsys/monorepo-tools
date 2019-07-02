# Front-end Breadcrumb Navigation

All frontend routes include breadcrumb navigation in the top of the page to ease the navigation for your customers across your e-commerce site.
When adding a new page on the frontend, you need to implement new `BreadcrumbGenerator` for the new routes to tell the application how the navigation should be displayed.

## How to create new `BreadcrumbGenerator`

- create new class with name ending with `BreadcrumbGenerator`
- this class has to implement `BreadcrumbGeneratorInterface`
- this interface requires you to implement two methods *(see [ArticleBreadcrumbGenerator](/packages/framework/src/Model/Article/ArticleBreadcrumbGenerator.php) class as an example of the implementation)*:
    - `getBreadcrumbItems` method that generates `BreadcrumbItems`
        - these include displayed name, and may include route and route parameters if you want to make a link from the item
    - `getRouteNames` method where you have to provide names of the routes for which you want to use your breadcrumb generator
- visit some URL matching your route and check if everything works fine

*Note: Administration breadcrumb navigations is generated from [Administration menu](/docs/administration/administration-menu.md#routing-extension).*
