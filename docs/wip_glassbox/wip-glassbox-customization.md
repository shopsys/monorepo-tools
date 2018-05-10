# WIP Glassbox Customization

## Migrations
* migrations now can be installed from all bundles registered in application, directory should be in bundle_root/Migrations folder

## Forms
State of forms is described much more in this [document](form-extension.md)

## Entities
* visibility of all private properties and methods of DataFactories was changed to protected (@Miroslav-Stopka)
    * protected visibility allows overriding of behavior from projects
* entities can be extended by inheritance
* all entities are extensible via `%shopsys.entity_extension.map%` parameter
* more info in separate article [Entity Extension](entity-extension.md)
* reasons and alternatives to this approach are explained in [Entity Extension vs. Entity Generation](entity-extension-vs-entity-generation.md)
