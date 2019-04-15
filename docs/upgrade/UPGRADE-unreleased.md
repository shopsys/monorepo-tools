# [Upgrade from v7.1.0 to Unreleased]

This guide contains instructions to upgrade from version v7.1.0 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Configuration
 - *(low priority)* use standard format for redis prefixes ([#928](https://github.com/shopsys/shopsys/pull/928))
    - change prefixes in `app/config/packages/snc_redis.yml` and `app/config/packages/test/snc_redis.yml`. Please find inspiration in [#928](https://github.com/shopsys/shopsys/pull/928/files)
    - once you finish this change, you still should deal with older redis cache keys that don't use new prefixes. Such keys are not removed even by `clean-redis-old`, please find and remove them manually (via console or UI)

    **Be careful, this upgrade will remove sessions**
 - postgresql.conf is now used by Postgres ([#])
    - update definition of postgres service in yourd `docker-compose.yml` file:
        ```diff
            postgres:
                image: postgres:10.5-alpine
                container_name: shopsys-framework-postgres
                volumes:
                    - ./project-base/docker/postgres/postgres.conf:/var/lib/postgresql/data/postgresql.conf:delegated
                    - ./project-base/var/postgres-data:/var/lib/postgresql/data:cached
                environment:
                    - PGDATA=/var/lib/postgresql/data/pgdata
                    - POSTGRES_USER=root
                    - POSTGRES_PASSWORD=root
                    - POSTGRES_DB=shopsys
            +   command: postgres -c config_file=/var/lib/postgresql/data/postgresql.conf
        ```


[Upgrade from v7.1.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.1.0...HEAD
