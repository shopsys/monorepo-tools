# Application Configuration
For operating Shopsys Framework it is needed to have correctly set connections to external services via `parameters.yml` config.
From the clean project, during composer installation process it will prompt you to set the values of parameters (`app/config/parameters.yml`):

| Name                                     | Description                                                                                                  |
| ---------------------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `database_host`                          | access data of your PostgreSQL database                                                                      |
| `database_port`                          | ...                                                                                                          |
| `database_name`                          | ...                                                                                                          |
| `database_user`                          | ...                                                                                                          |
| `database_password`                      | ...                                                                                                          |
| `database_server_version`                | version of your PostgreSQL server                                                                            |
| `elasticsearch_host`                     | host of your Elasticsearch                                                                                   |
| `redis_host`                             | host of your Redis storage (credentials are not supported right now)                                         |
| `mailer_transport`                       | access data of your mail server                                                                              |
| `mailer_host`                            | ...                                                                                                          |
| `mailer_user`                            | ...                                                                                                          |
| `mailer_password`                        | ...                                                                                                          |
| `mailer_disable_delivery`                | set to `true` if you don't want to send any e-mails                                                          |
| `mailer_master_email_address`            | set if you want to send all e-mails to one address (useful for development)                                  |
| `mailer_delivery_whitelist`              | set as array with regex text items if you want to have master e-mail but allow sending to specific addresses |
| `secret`                                 | randomly generated secret token                                                                              |
| `trusted_proxies`                        | proxies that are trusted to pass traffic, used mainly for production                                         |
| `env(REDIS_PREFIX)`                      | separates more projects that use the same redis service                                                      |
| `env(ELASTIC_SEARCH_INDEX_PREFIX)`       | separates more projects that use the same elasticsearch service                                              |

Composer will then prompt you to set parameters for testing environment (`app/config/parameters_test.yml`):

| Name                               | Description                                                                   |
| ---------------------------------- | ----------------------------------------------------------------------------- |
| `test_database_host`               | access data of your PostgreSQL database for tests                             |
| `test_database_port`               | ...                                                                           |
| `test_database_name`               | ...                                                                           |
| `test_database_user`               | ...                                                                           |
| `test_database_password`           | ...                                                                           |
| `overwrite_domain_url`             | overwrites URL of all domains for acceptance testing (set to `~` to disable)  |
| `selenium_server_host`             | with native installation the selenium server is on `localhost`                |
| `test_mailer_transport`            | access data of your mail server for tests                                     |
| `test_mailer_host`                 | ...                                                                           |
| `test_mailer_user`                 | ...                                                                           |
| `test_mailer_password`             | ...                                                                           |
| `shopsys.content_dir_name`         | web/content-test/ directory is used instead of web/content/ during the tests  |

*Notes:*
- *All default values use default ports for all external services like PostgreSQL database, elasticsearch, redis, ...*
- *Host values can be modified or can be aliased for your Operating System via `/etc/hosts` or `C:\Windows\System32\drivers\etc\hosts`*
