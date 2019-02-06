# Design Implementation and Customization
Here are the basic technologies we use in Shopsys Framework for design implementation:
- [LESS pre-processor](http://lesscss.org/) for definition of cascading style sheets (i.e. [CSS](https://www.w3.org/Style/CSS/Overview.en.html))
    - the LESS files are located in `src/Shopsys/ShopBundle/Resources/styles`  
    - you can read more about LESS in separate article [Introduction to Less](./introduction-to-less.md)
- [Grunt task runner](https://gruntjs.com/) for generation of CSS from LESS
    - `Gruntfile.js` is generated from `src/Shopsys/ShopBundle/Resources/views/Grunt/gruntfile.js.twig` during application build using `gruntfile` by [phing target](../introduction/console-commands-for-application-management-phing-targets.md)
- [Twig templating engine](https://twig.symfony.com/) for definition of HTML (and other) templates
    - the Twig templates are located in `src/Shopsys/ShopBundle/Resources/views`

When you want to customize the styles or templates, you can modify any of the files directly, as all of them are located in `ShopBundle` (i.e. in your project).

## Multidomain design customization
Shopsys Framework provides an ability of running multiple domains as a single application,
if you want to know more about this concept, you can read [the separate article](../introduction/domain-multidomain-multilanguage.md).
In order to change your multidomain appearance, you can set two parameters [`domains.yml`](/project-base/app/config/domains.yml) configuration file:
- `styles_directory`
    - allows you to define a custom sub-folder with LESS files in `src/Shopsys/ShopBundle/Resources/styles`
    - if you need to use custom styles for a particular domain, put your LESS files in this sub-folder
    - you can create your own directories structure in the sub-folder that suits your needs
- `design_id`
    - allows you to define a design identifier
    - the parameter can be a number (e.g. domain ID), however, you can use a string identifier as well (e.g. "flat-design") so you are able to use the same design across multiple domains
    - if you want to use custom template for a particular domain, duplicate the original one that is used for the first domain and add `.design_id` value as a suffix to its name (e.g. `detail.html.twig` -> `detail.flat-design.html.twig`)
    - all the multi-design templates must be located in the same folders as their originals
        - there is a huge advantage from the usability point of view - when you change a controller, you need to change all the related multi-design templates.
        In such a case, you see all the templates in the same folder and you do not need to seek for them anywhere else

If you want to know exact instructions on what to do when implementing a custom multidomain design, check [Creating a Multidomain Design cookbook](../cookbook/creating-a-multidomain-design.md).
