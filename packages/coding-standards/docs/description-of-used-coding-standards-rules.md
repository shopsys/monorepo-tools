# Description of Used Coding Standards Rules

The tables below contain list of custom coding standards rules.


## Rules checked with [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

| Rule name                                     | Rule description                                                                                                                   | 
| --------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- | 
| `Shopsys/missing_button_type`                 | Adds mandatory `type` attribute to `<button>` HTML tag.                                                                            |
| `Shopsys/orm_join_column_require_nullable`    | Doctrine annotations `@ORM\ManyToOne` and `@ORM\OneToOne` must have defined `nullable` option in `@ORM\JoinColumn`.                |
| `Shopsys/no_unused_imports`                   | Unused use statements (except those from the same namespace) must be removed.                                                      |

## Rules checked with [PHPMD](https://github.com/phpmd/phpmd)

You can see [official documentation](https://phpmd.org/rules/index.html) for more information about the rules.

| Rule name                                           | Rule description                                                                                                                   | Note                                         |
| ---------------------------------------------       | ---------------------------------------------------------------------------------------------------------------------------------- | ----                                         |
| `CamelCasePropertyName`                             | Property names must be in camelCase                                                                                                | Custom rule. Can not be fixed automatically. |
