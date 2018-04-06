# Installation Using Docker for MacOS

This solution uses [*docker-sync*](http://docker-sync.io/) (for fast two-way synchronization of the application files between the host machine and Docker volume).

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [Docker for Mac](https://docs.docker.com/engine/installation/) (Docker-sync suggests ([in known issue](https://github.com/EugenMayer/docker-sync/issues/517)) to use Docker for Mac in version 17.09.1-ce-mac42 (21090)) 
* [Docker-sync](http://docker-sync.io/) (install via `sudo gem install docker-sync`)

## Steps
### 1. Create new project from Shopsys Framework sources
```
composer create-project shopsys/project-base --stability=alpha --no-install
cd project-base
```
Notes: 
- The `--no-install` option disables installation of the vendors - this will be done later in the Docker container
- If you want to keep the GIT history of `shopsys/project-base` in your new project, use the `--keep-vcs` option

### 1.1 Enable second domain (optional)
There are two domains each for different language in default installation. First one is available via IP adress `127.0.O.1` and second one via `127.0.0.2`.
`127.0.0.2` is not alias of `127.0.0.1` on Mac by default. To create this alias in network interface run:
```
sudo ifconfig lo0 alias 127.0.0.2 up
```

### 2. Create docker-compose.yml file
Create `docker-compose.yml` from template [`docker-compose-mac.yml.dist`](../../../docker/conf/docker-compose-mac.yml.dist).
```
cp docker/conf/docker-compose-mac.yml.dist docker-compose.yml
```

### 3. Compose Docker container
On MacOS you need to synchronize folders using docker-sync.
Before starting synchronization you need to create a directory for persisting Postgres data so you won't lose it when the container is shut down.
```
mkdir -p var/postgres-data
docker-sync start
```

Then start containers
```
docker-compose up -d
```

### 4. Set file permissions
Grant system users inside the container the required permissions
#### Connect into terminal of the Docker container
```
docker exec -it shopsys-framework-php-fpm bash
```

#### Allow user with UID 33 ("www-data" in "php-fpm" container) read and write all project files
```
setfacl -R -m user:33:rwX -m mask:rwX .
setfacl -dR -m user:33:rwX -m mask:rwX .
```

#### Allow user with UID 100 ("nginx" in "webserver" container) read files in "web" directory
```
setfacl -R -m user:100:rX ./web
setfacl -dR -m user:100:rX ./web
```

### 5. Setup the application
[Application setup guide](installation-using-docker-application-setup.md)