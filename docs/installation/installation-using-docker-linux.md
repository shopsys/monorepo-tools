# Installation Using Docker for Linux

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PHP](http://php.net/manual/en/install.unix.php)
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* [Docker](https://docs.docker.com/engine/installation/)

## Steps
### 1. Create new project from Shopsys Framework sources
```
composer create-project shopsys/project-base --stability=alpha --no-install --keep-vcs
cd project-base
```

*Note: The `--no-install` option disables installation of the vendors - this will be done later in the Docker container.*

*Note: The `--keep-vcs` option initializes GIT repository in your project folder that is needed for diff commands of the application build and keeps the GIT history of `shopsys/project-base`.*

*Note: The `--stability=alpha` option enables you to install the project from the last alpha release. Default value for the option is `stable` but there is no stable release yet.*
 
### 2. Create docker-compose.yml file
Create `docker-compose.yml` from template [`docker-compose.yml.dist`](../../project-base/docker/conf/docker-compose.yml.dist).
```
cp docker/conf/docker-compose.yml.dist docker-compose.yml
```

*Note: If you don't plan any custom configuration you can create a symlink with a command `ln -s docker/conf/docker-compose.yml.dist docker-compose.yml`.*
*This way your config will always be in sync with the template.*

### 3. Set the UID and GID to allow file access in mounted volumes
Because we want both the user in host machine (you) and user running php-fpm in the container to access shared files, we need to make sure that they both have the same UID and GID.
This can be achieved by build arguments `www_data_uid` and `www_data_gid` that should be set to the UID and GID as your own user in your `docker-compose.yml`.

You can find out your UID by running `id -u` and your GID by running `id -g`.

### 4. Compose Docker container
```
docker-compose up -d --build
```

### 5. Setup the application
[Application setup guide](installation-using-docker-application-setup.md)
