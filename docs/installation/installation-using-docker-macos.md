# Installation Using Docker for MacOS

This solution uses [*docker-sync*](http://docker-sync.io/) (for fast two-way synchronization of the application files between the host machine and Docker volume).

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PHP](http://php.net/manual/en/install.macosx.php)
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* [Docker for Mac](https://docs.docker.com/engine/installation/)
    * Docker-sync suggests ([in known issue](https://github.com/EugenMayer/docker-sync/issues/517)) to use Docker for Mac in version 17.09.1-ce-mac42 (21090)
    * Docker for Mac requires at least 4 GB of memory, otherwise, `composer install` can result in `Killed` status (we recommend to set 2 GB RAM, 1 CPU and 2 GB Swap in `Docker -> Preferences… -> Advanced`)
* [Docker-sync](http://docker-sync.io/) (install via `sudo gem install docker-sync`)

## Steps
### 1. Create new project from Shopsys Framework sources
```
composer create-project shopsys/project-base --stability=beta --no-install --keep-vcs
cd project-base
```

*Notes:*
- *The `--no-install` option disables installation of the vendors - this will be done later in the Docker container.*
- *The `--keep-vcs` option initializes GIT repository in your project folder that is needed for diff commands of the application build and keeps the GIT history of `shopsys/project-base`.*
- *The `--stability=beta` option enables you to install the project from the last beta release. Default value for the option is `stable` but there is no stable release yet.*

### 1.1 Enable second domain (optional)
There are two domains each for different language in default installation. First one is available via IP adress `127.0.O.1` and second one via `127.0.0.2`.
`127.0.0.2` is not alias of `127.0.0.1` on Mac by default. To create this alias in network interface run:
```
sudo ifconfig lo0 alias 127.0.0.2 up
```

### 2. Create docker-compose.yml and docker-sync.yml
Create `docker-compose.yml` from template [`docker-compose-mac.yml.dist`](../../project-base/docker/conf/docker-compose-mac.yml.dist).
```
cp docker/conf/docker-compose-mac.yml.dist docker-compose.yml
```

Create `docker-sync.yml` from template [`docker-sync.yml.dist`](../../project-base/docker/conf/docker-sync.yml.dist).
```
cp docker/conf/docker-sync.yml.dist docker-sync.yml
```

#### Set the UID and GID to allow file access in mounted volumes
Because we want both the user in host machine (you) and the user running php-fpm in the container to access shared files, we need to make sure that they both have the same UID and GID.
This can be achieved by build arguments `www_data_uid` and `www_data_gid` that should be set to the same UID and GID as your own user in your `docker-compose.yml`.
Also, you need to change `sync_userid` in `docker-sync.yml` file.

You can find out your UID by running `id -u` and your GID by running `id -g`.

Once you get these values, set these values into your `docker-compose.yml` into `php-fpm` container definition by replacing values in `args` section.

Also you need to insert your UID into `docker-sync.yml` into value `sync_userid`.

#### Set the Github token in your docker-compose.yml file
Shopsys Framework includes a lot of dependencies installed via Composer.
During `composer install` the GitHub API Rate Limit is reached and it is necessary to provide GitHub OAuth token to overcome this limit.
This token can be generated on [Github -> Settings -> Developer Settings -> Personal access tokens](https://github.com/settings/tokens/new?scopes=repo&description=Composer+API+token)
Save your token into the `docker-compose.yml` file.
Token is located in `services -> php-fpm -> build -> args -> github_oauth_token`.

<!--- TODO When releasing new version, remove the section "Set the Github token in your docker-compose.yml file" as (in current master)  docker-compose.yml and dockerfiles don't contain or operate with such attribute -->

### 3. Compose Docker container
On MacOS you need to synchronize folders using docker-sync.
Before starting synchronization you need to create a directory for persisting Postgres and Elasticsearch data so you won't lose it when the container is shut down.
```
mkdir -p var/postgres-data var/elasticsearch-data vendor
docker-sync start
```

Then rebuild and start containers
```
docker-compose up -d --build

```

### 4. Setup the application
[Application setup guide](installation-using-docker-application-setup.md)
