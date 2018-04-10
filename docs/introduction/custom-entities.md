# Custom Entities

When you need to add functionality that is not in the system, like an advertising campaign,
then you need your own, custom entities.

* The system is prepared and configured for custom entities.
The configuration is placed in [config.yml](/project-base/app/config/config.yml), section `doctrine.orm.mappings`.
* An entity should be in namespace `Shopsys\ShopBundle\Model` (directory `src/Shopsys/ShopBundle/Model`).
* We use annotations for Doctrine mapping.
More in [annotations reference](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html).
* And maybe you are also interested in [entity extension](/docs/wip_glassbox/entity-extension.md).

## Example

`CustomEntity` can look like:

```php
// src/Shopsys/ShopBundle/Model/CustomEntity.php

namespace Shopsys\ShopBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CustomEntity
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    //...
}
```

When the entity is completed, you have to ensure the system registers it properly.
A convinient way is to [generate migration](phing-targets.md#db-migrations-generate).
* Execute console command `./phing db-migrations-generate`
* We encourage you to check this migration if everything is set as expected.
If the system doesn't generate the migration, the entity is probably in an incorrect namespace or has wrong Doctrine annotation mapping.

If the migration is fine, you can continue the work and eventually
[execute migrations](phing-targets.md#db-migrations).
* Execute console command `./phing db-migrations`
