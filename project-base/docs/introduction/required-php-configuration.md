# Required PHP Configuration
This is a recommended configuration of PHP for project development using Shopsys Framework.

## Recommended `php.ini` settings
```
; do not recognize code between <? and ?> tags as PHP source 
short_open_tag = Off

; some development CLI commands can be memory consuming
memory_limit = 512M

; enables upload of files up to 32 MB
post_max_size = 32M
upload_max_filesize = 32M

; report all errors in development environment
error_reporting = E_ALL

; for better performance of Symfony applications especially on Windows
; source: http://symfony.com/doc/3.2/performance.html
realpath_cache_size = 4096k
realpath_cache_ttl = 600
opcache.enable = 1
opcache.max_accelerated_files = 20000
```

## Required PHP extensions
| Extension name | Reason                                                                                                                                                        |
| -------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| bcmath         | required by package `commerceguys/intl`                                                                                                                       |
| ctype          | used by various packages; should be present by default since PHP 4.2.0 but on some systems (like FreeBSD) it can be optional                                  |
| curl           | needed by package `heureka/overeno-zakazniky` to work correctly; see https://github.com/heureka/overeno-zakazniky/issues/21                                   |
| gd             | used by `Shopsys\ShopBundle\Component\Image` for generating images                                                                                            |
| iconv          | used by `Shopsys\ShopBundle\Component\String\*` classes                                                                                                       |
| intl           | needed because `Symfony\Intl` component supports only `en` locale                                                                                             |
| json           | needed for `json_encode` and `json_decode` functions; should be present by default since PHP 5.2.0 but on some systems (like Ubuntu 13.10) it can be optional |
| mbstring       | needed for `mb_str*` functions                                                                                                                                |
| opcache        | optional but highly recommended for better performance                                                                                                        |
| openssl        | needed by Composer for secure communication                                                                                                                   |
| pdo            | required by package `doctrine/orm`                                                                                                                            |
| pdo_pgsql      | required to support `pdo_pgsql` database driver                                                                                                               |
| pgsql          | used in acceptance for fast repopulating of database using `COPY` command                                                                                     |
| simplexml      | used by `Shopsys\ShopBundle\Model\Feed\Category\HeurekaFeedCategoryLoader`                                                                                    |
| xml            | used by Phing for XML parsing                                                                                                                                 |
| zip            | used by `Shopsys\ShopBundle\Command\ImageDemoCommand` to extract demo images                                                                                  |
