# Installation Using Docker for Linux

**This guide is for the version which is not released yet. See the [version for `v7.2.1`](https://github.com/shopsys/shopsys/blob/v7.2.1/docs/installation/installation-using-docker-linux.md).**

This guide covers building new projects based on Shopsys Framework.
If you want to contribute to the framework itself,
you need to install the whole [shopsys/shopsys](https://github.com/shopsys/shopsys) monorepo.
Take a look at the article about [Monorepo](../introduction/monorepo.md) for more information.

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PHP](http://php.net/manual/en/install.unix.php)
    * At least version **7.2 or higher**
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* [Docker](https://docs.docker.com/engine/installation/)
    * At least version **17.05 or higher** so it supports [multi-stage builds](https://docs.docker.com/develop/develop-images/multistage-build/).
* [Docker Compose](https://docs.docker.com/compose/install/)
    * At least version **1.17.0 or higher** because we use compose file version `3.4`

## Steps
### 1. Create new project from Shopsys Framework sources
```
composer create-project shopsys/project-base --no-install --keep-vcs
cd project-base
```

*Notes:*
- *The `--no-install` option disables installation of the vendors - this will be done later in the Docker container.*
- *The `--keep-vcs` option initializes GIT repository in your project folder that is needed for diff commands of the application build and keeps the GIT history of `shopsys/project-base`.*

### 2.1 Use install script
In case you want to start demo of the app as fast as possible, you can now execute install script.

```
./scripts/install.sh
```

If you want to know more about what is happening during installation, continue with next step.

### 2.2 Create docker-compose.yml file
Create `docker-compose.yml` from template [`docker-compose.yml.dist`](../../project-base/docker/conf/docker-compose.yml.dist).
```
cp docker/conf/docker-compose.yml.dist docker-compose.yml
```

### 3. Set the UID and GID to allow file access in mounted volumes
Because we want both the user in host machine (you) and user running php-fpm in the container to access shared files, we need to make sure that they both have the same UID and GID.
This can be achieved by build arguments `www_data_uid` and `www_data_gid` that should be set to the UID and GID as your own user in your `docker-compose.yml`.

You can find out your UID by running `id -u` and your GID by running `id -g`.

### 4. Compose Docker container
```
docker-compose up -d --build
```

***Note:** During the build of the docker containers there will be installed 3-rd party software as dependencies of Shopsys Framework by [Dockerfile](https://docs.docker.com/engine/reference/builder/) with licenses that are described in document [Open Source License Acknowledgements and Third-Party Copyrights](../../open-source-license-acknowledgements-and-third-party-copyrights.md)*

### 5. Setup the application
[Application setup guide](installation-using-docker-application-setup.md)
