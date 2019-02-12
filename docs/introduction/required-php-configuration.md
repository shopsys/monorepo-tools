# Required PHP Configuration
This is a recommended configuration of PHP for project development using Shopsys Framework.

## Recommended `php.ini` settings
```
; do not recognize code between '<?' and '?>' tags as PHP source
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

; enable OpCache (otherwise Symfony will be slow)
opcache.enable=1

; faster mechanism for calling the deconstructors in your code at the end of a single request
opcache.fast_shutdown = true

; The amount of memory used to store interned strings, in megabytes
opcache.interned_strings_buffer = 24

; Optimizations for Symfony, as documented on http://symfony.com/doc/current/performance.html
opcache.max_accelerated_files = 60000

; The size of the shared memory storage used by OPcache, in megabytes
opcache.memory_consuption = 256

; always resolve symlinks
opcache.revalidate_path=1

; how often to check script timestamps for updates. 0 will result in opcache checking
; for updates on every request. Recommended value for production is 300
opcache.revalidate_freq = 0

; use absolute paths, so that there are not collision for files with same names
opcache.use_cwd=1
```

## Required PHP extensions
| Extension name | Reason                                                                                                                                                        |
| -------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| bcmath         | required by package `commerceguys/intl`                                                                                                                       |
| ctype          | used by various packages; should be present by default since PHP 4.2.0 but on some systems (like FreeBSD) it can be optional                                  |
| curl           | needed by package `heureka/overeno-zakazniky` to work correctly; see https://github.com/heureka/overeno-zakazniky/issues/21                                   |
| filter         | used for `filter_var` by `Shopsys\FrameworkBundle\Model\Cart\Item\CartItem`                                                                                   |
| gd             | used by `Shopsys\FrameworkBundle\Component\Image` for generating images                                                                                            |
| iconv          | used by `Shopsys\FrameworkBundle\Component\String\*` classes                                                                                                       |
| intl           | needed because `Symfony\Intl` component supports only `en` locale                                                                                             |
| json           | needed for `json_encode` and `json_decode` functions; should be present by default since PHP 5.2.0 but on some systems (like Ubuntu 13.10) it can be optional |
| libxml         | used for `LIBXML_*` constants by `Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDownloader`                                          |
| mbstring       | needed for `mb_str*` functions                                                                                                                                |
| opcache        | optional but highly recommended for better performance                                                                                                        |
| openssl        | needed by Composer for secure communication                                                                                                                   |
| pdo            | required by package `doctrine/orm`                                                                                                                            |
| pdo_pgsql      | required to support `pdo_pgsql` database driver                                                                                                               |
| pgsql          | used in acceptance for fast repopulating of database using `COPY` command                                                                                     |
| redis          | required by package `snc/redis-bundle` and sessions stored in Redis (minimal version is 4.1.1 because of lazyloading)                                        |
| simplexml      | used by Heureka product feed module in `\Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryCronModule`                                   |
| tokenizer      | used for `T_*` constants by `shopsys\coding-standards` package                                                                                                |
| xml            | used by Phing for XML parsing                                                                                                                                 |
| zip            | used by facebook package during the acceptance tests                                                                                  |
