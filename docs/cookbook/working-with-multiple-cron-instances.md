# Working with Multiple Cron Instances
This cookbook will help you to set up independent processing of [cron jobs](/docs/introduction/cron.md).
We will learn, how to work with multiple cron instances, how to register a new instance for modules and how to run them.

Let's presume, we want to run import of products, created for [Basic Data Import](./basic-data-import.md).
But this import might take some time and we do not want to block processing of the other cron modules.

## Configuration
When you register cron job in your configuration with tags mandatory for cron module, you can add the optional tag `instanceName`,
effectively creating a new instance of cron just by tagging the service.  
You do not have to register the instance anywhere else.

We just edit earlier created configuration to place our `ImportProductsCronModule` to different cron instance.
```diff
# src/Shopsys/ShopBundle/Resources/config/services/cron.yml

services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false

    Shopsys\ShopBundle\Model\Product\ImportProductsCronModule:
        class: Shopsys\ShopBundle\Model\Product\ImportProductsCronModule
        tags:
-            - { name: shopsys.cron, hours: '*/3', minutes: '0' }
+            - { name: shopsys.cron, hours: '*/3', minutes: '0', instanceName: products}
```

*Note: If you do not set `instanceName`, a job will be placed into cron instance named `default`.*

## Listing available modules
Now by running `php phing cron-list` in a console, we can see a list of all available cron modules, grouped into cron instances.
`ImportProductsCronModule` is properly placed into cron instance named "products".

```
products
--------

 php bin/console shopsys:cron --module="Shopsys\ShopBundle\Model\Product\ImportProductsCronModule" --instance-name=products

default
-------

 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Component\Error\ErrorPageCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Cart\Item\DeleteOldCartsCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Feed\DailyFeedCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Feed\HourlyFeedCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Pricing\Vat\VatDeletionCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Product\ProductVisibilityImmediateCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Product\ProductVisibilityMidnightCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\FrameworkBundle\Model\Sitemap\SitemapCronModule" --instance-name=default
 php bin/console shopsys:cron --module="Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryCronModule" --instance-name=default
```

*Note: More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

# Running cron jobs manually
We now can run any cron jobs manually by running `php phing cron`.
And because we have several cron instances registered, job asks what cron instance should be run.

*Note: If only one instance is registered, no question is asked and this instance will run immediately.*

# Running cron jobs automatically
To be able to run cron instances automatically, we first have to create new Phing targets in `build.xml` configuration file.
These targets have to be able to run without asking any interactive questions (`--instance-name` argument does the trick).

New targets would look like

```xml
<target name="cron-default" description="Runs default background jobs. Should be executed periodically by system Cron every 5 minutes.">
    <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
        <arg value="${path.bin-console}" />
        <arg value="shopsys:cron" />
        <arg value="--instance-name=default" />
    </exec>
</target>

<target name="cron-products" description="Runs background jobs for import of products. Should be executed periodically by system Cron every 5 minutes.">
    <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
        <arg value="${path.bin-console}" />
        <arg value="shopsys:cron" />
        <arg value="--instance-name=products" />
    </exec>
</target>
```

and these targets only have to be registered in system crontab.

## Pitfalls
- If you tag cron module with another instance without changes in Phing targets, your jobs will not be executed automatically, because command will hold on instance choice question.
- You can easily set your system to run too much cron jobs at once, resulting in server response time slowdown.
