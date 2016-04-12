## To be able to use this bundle, you need to do following:

1. In your applications ```composer.json``` add repository
```
"type": "vcs",
"url": "https://github.com/shopsys/migrations.git"
```
2. Require ```shopsys/migrations```
3. Register bundles in your ```app/AppKernel```:
```
new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
new ShopSys\MigrationBundle\ShopSysMigrationBundle(),
```
4. Configure doctrine_migrations in your ```app/config.yml``` (see https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html#configuration)