# Code Quality Principles

We strictly follow [coding standards](coding-standards.md) already and we are committing ourselves to improve Shopsys code quality beyond coding standards.
We decided to write down principles that improve code quality so the code quality is consistent between team members.
Due to the written form, we have a place to point during code review to cut down discussions.

These principles below are not only principles we follow, but the important ones that we agreed in the core team and that are unlikely to be violated.
We may add, change or remove principles as we improve our practices, so this is more of a living document than a definitive guide.

### When do we follow principles

We follow principles during writing **new code**, during **changing the current code** and we check principles during **code review**.

You can find an old code that doesn't follow these principles and we are ok with that as we are not able to refactor the whole framework at once.
It may also happen that changed code cannot follow a principle because it has to be compatible with our [backward compatibility promise](backward-compatibility-promise.md), backward compatibility is more important than the code quality.

These principles are not rules, they can be violated when it has a reason.
The reason has to be discussed with the reviewer and may be written in the commit message.

## Principles

* [Dependency injection](#dependency-injection)
* [Strict types](#strict-types)
* [Don't repeat yourself](#dont-repeat-yourself)
* [Dependency inversion](#dependency-inversion)

## Dependency injection

Dependency injection (or DI) means that an object doesn't create dependencies by itself but receives dependencies by a provider.
We use constructor DI only.

```diff
class LocalizedType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

-   public function __construct()
-   {
-       $this->localization = new Localization();
-   }
+   /**
+   * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
+   */
+   public function __construct(Localization $localization)
+   {
+       $this->localization = $localization;
+   }

    // ...
}
```

### Reasons

It may be difficult to create the dependency - it may have also dependencies or configuration.
Once creating the dependency is not the class responsibility, the class code is much more straight-forward.

We inject only dependencies that the class needs, it is forbidden to pass the service locator to the class.
With service locator injection it is difficult to understand what does the class really do (or need) and such class is difficult to test.

We use constructor DI so the class is always in a consistent state.

It is essential to provide code extensibility in the framework and this is achieved with proper DI.

### Exceptions

We don't respect this principle in tests as it is impossible to pass dependencies to PHPUnit tests via the constructor.
In tests we use `$this->getContainer()->get(...)`.

## Strict types

`strict_types` is a directive that enforces PHP to check the type of scalar types (`int`, `float`, `string`, `bool`) strictly.

```diff
+ declare(strict_types=1);

final class Money implements JsonSerializable
{
    // ...

    public static function createFromFloat(float $float, int $scale): self
    // ...
}
```

If the caller of `createFromFloat()` method provides different arguments than declared, PHP throws a `TypeError`.
This leads to safer code.

### Reasons

We use scalar type declarations (`int`, `float`, `string`, `bool`) as much as possible in new code.
Using scalar type declarations without strict mode leads to automatic type cast that can be even harmful (eg. input string `""` is cast to integer `0`).
We prevent this risky automatic type cast by declaring strict types.

Please read more about scalar types and strict mode in [Scalar Type Declarations](https://wiki.php.net/rfc/scalar_type_hints_v5).

## Don't repeat yourself

> Every piece of knowledge must have a single, unambiguous, authoritative representation within a system.

DRY is one of the most well-known programming principles and it was already written so much about this principle that it doesn't make sense to write it once again.
Please find more about DRY in online sources or in the great book [The Pragmatic Programmer](https://pragprog.com/book/tpp/the-pragmatic-programmer) where this principle was formulated for the first time.

### Exceptions

We understand the DRY principle in a pragmatic way, we aren't obsessed with a duplication in the system that makes sense.
Usability and maintainability are often more important than the DRY principle.

Examples of such duplication are definitions of [Elasticsearch indexes](/project-base/src/Shopsys/ShopBundle/Resources/definition/product).
They are very, very similar but if they were done in a strictly DRY fashion, the definition would have to be done in PHP and therefore less readable and maintainable than in JSON format.
Also, it would be difficult to change the shared code just for one use-case.

## Dependency inversion

Modules don't depend on details of each other, modules depend on interfaces.

```diff
class SearchController extends FrontBaseController
{
    /**
-    * @var \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacade
+    * @var \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface
     */
    private $productOnCurrentDomainFacade;

    // ...
```

```diff
- class ProductOnCurrentDomainElasticFacade
+ class ProductOnCurrentDomainElasticFacade implements ProductOnCurrentDomainFacadeInterface
```

You should use an interface to communicate with a different module.
The most typical place is `project-base | framework` so you should keep this principle in mind during programming anything that is related to both `project-base` and `framework`.

### Reasons

In a traditional dependency system, the top-most class depends on the lower layer, which depends on the lower layer and so on.
In the end, the top-most class indirectly depend on all lower classes in multiple modules.

```
Controller --> Facade --> Repository --> Doctrine
```

When the interface is introduced, this nested dependency is broken.

```
Controller --> FacadeInterface
                      ^
                      |
                  SQLFacade --> Repository --> Doctrine
```

The most important advantage of this interface dependency system is that the implementation (`SQLFacade`) is replaceable.
This gives you more freedom during a shop implementation.
If you have a use-case that is not achievable by the framework implementation, you are able to replace it on your own.

### Misuse

This principle is valid only during the cooperation of modules.
Don't try to use this principle within a single module (eg. framework), it won't make any sense.
