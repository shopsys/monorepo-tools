# [Upgrade from 7.0.0-alpha1 to 7.0.0-alpha2](https://github.com/shopsys/shopsys/compare/v7.0.0-alpha1...v7.0.0-alpha2)

This guide contains instructions to upgrade from version 7.0.0-alpha1 to 7.0.0-alpha2.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/project-base]  
- check changes in the `docker-compose.yml` template you used, there were a couple of important changes you need to replicate
    - easiest way is to overwrite your `docker-compose.yml` with by the appropriate template
- on *nix systems, fill your UID and GID (you can run `id -u` and `id -g` to obtain them) into Docker build arguments `www_data_uid` and `www_data_gid` and rebuild your image via `docker-compose up --build`
- change owner of the files in shared volume to `www-data` from the container by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /var/www/html`
    - the user has shared UID, so you will be able to access it as well from the host machine
    - shared volume with postgres data should be owned by `postgres` user: `docker exec -u root shopsys-framework-php-fpm chown -R postgres /var/www/html/var/postgres-data`
- if you were using a mounted volume to share Composer cache with the container, change the target directory from `/root/.composer` to `/home/www-data/.composer`
    - in such case, you should change the owner as well by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /home/www-data/.composer`

[shopsys/project-base]: https://github.com/shopsys/project-base
