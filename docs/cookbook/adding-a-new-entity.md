# Adding a New Entity
This article provides step by step instructions on how to add a new entity to your project.
Basic information about custom entities can be found in the [separate article](/docs/introduction/custom-entities.md).

Let's say we need to keep an agenda of salesmen. After finishing this cookbook, the new salesman entity will not be presented on the FE in any fashion,
however, you can continue with another [cookbook](/docs/cookbook/create-basic-grid.md) that will show you how to display a list of salesmen using a grid in administration.

## 1. Create a new class `Salesman` and set it as an entity using Doctrine annotation
```php
// src\Shopsys\ShopBundle\Model\Salesman\Salesman.php

namespace Shopsys\ShopBundle\Model\Salesman;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="salesmen")
 * @ORM\Entity
 */
class Salesman
{
}
```

## 2. Add salesmen attributes and set them as database columns using Doctrine annotations
Each salesman entity will have the following properties.
- `id` - unique sequenced value for salesman identification
- `name` - name of the salesman limited to 100 characters
- `registeredAt` - registration date of the salesman

```diff
namespace Shopsys\ShopBundle\Model\Salesman;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="salesmen")
 * @ORM\Entity
 */
class Salesman
{
+    /**
+     * @var int
+     *
+     * @ORM\Column(type="integer")
+     * @ORM\Id
+     * @ORM\GeneratedValue(strategy="IDENTITY")
+     */
+    protected $id;
+
+    /**
+     * @var string
+     *
+     * @ORM\Column(type="string", length=100)
+     */
+    protected $name;
+
+    /**
+     * @var \DateTime
+     *
+     * @ORM\Column(type="datetime")
+     */
+    protected $registeredAt;
}
```

## 3. Generate a database migration
Run a console command (in `php-fpm container` if you are using Docker) that will generate a database migration for you:
 ```bash
php phing db-migrations-generate
```

*Note: More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

The command will print a filename of the database migration with content like this.
```php
namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190301122526 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE salesmen (
                id SERIAL NOT NULL,
                name VARCHAR(100) NOT NULL,
                registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
```
*Note: We recommend you to check this migration whether everything is set as expected. If the system doesn't generate the migration, the entity is probably in an incorrect namespace or has wrong Doctrine annotation mapping.*

## 4. Add default salesmen
Now we add some entries into the new database table by modifying the database migration.
```diff
namespace Shopsys\ShopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20190301122526 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('
            CREATE TABLE salesmen (
                id SERIAL NOT NULL,
                name VARCHAR(100) NOT NULL,
                registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )');

+        $this->sql('INSERT INTO salesmen (name, registered_at) VALUES (\'John Lennon\', \'2019-03-01 12:00:00\')');
+        $this->sql('INSERT INTO salesmen (name, registered_at) VALUES (\'Paul McCartney\', \'2018-04-19 15:25:42\')');
+        $this->sql('INSERT INTO salesmen (name, registered_at) VALUES (\'George Harrison\', \'2019-01-11 09:30:15\')');
+        $this->sql('INSERT INTO salesmen (name, registered_at) VALUES (\'Ringo Starr\', \'2016-12-12 18:42:00\')');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
```

## 5. Execute migrations to propagate all the changes to database
Run a console command
```bash
php phing db-migrations
```

## Conclusion
Now, there is a new entity in your system - `Salesmen` - for which exists a database table that has 4 records with salesmen.
If you want to display a list of them in the administration, follow ["Create basic grid" cookbook](/docs/cookbook/create-basic-grid.md).
