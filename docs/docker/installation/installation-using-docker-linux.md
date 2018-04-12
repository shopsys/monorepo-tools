# Installation Using Docker for Linux

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [Docker](https://docs.docker.com/engine/installation/)

## Steps
### 1. Create new project from Shopsys Framework sources
```
composer create-project shopsys/project-base --stability=alpha --no-install --keep-vcs
cd project-base
```
Notes: 
- The `--no-install` option disables installation of the vendors - this will be done later in the Docker container
- If you want to keep the GIT history of `shopsys/project-base` in your new project, use the `--keep-vcs` option
 
### 2. Create docker-compose.yml file
Create `docker-compose.yml` from template [`docker-compose.yml.dist`](../../../project-base/docker/conf/docker-compose.yml.dist).
```
cp docker/conf/docker-compose.yml.dist docker-compose.yml
```

*Note: If you don't plan any custom configuration you can create a symlink with a command `ln -s docker/conf/docker-compose.yml.dist docker-compose.yml`.*
*This way your config will always be in sync with the template.*

### 3. Compose Docker container
```
docker-compose up -d
```

### 4. Set file permissions
#### 4.1. Grant your local user permissions
On Linux, synchronization between your local directory and container directory is done by mount. That means that a local directory is shared with the one in container.

This brings fast synchronization between directories. The problem is that all commands executed in container are executed as root user which means that every file that is created by a command  creates belong to him and you cannot edit them.

Thankfully we can use `setfacl` in order to grant the user on your host machine permissions to all existing and even newly created files.  

##### Make the current and future project files accessible by the current user on your host system
```
sudo setfacl -R -m user:`whoami`:rwX -m mask:rwX .
sudo setfacl -dR -m user:`whoami`:rwX -m mask:rwX .
```

#### 4.2. Grant system users inside the container the required permissions
##### Connect into terminal of the Docker container
```
docker exec -it shopsys-framework-php-fpm bash
```

##### Allow user with UID 33 ("www-data" in "php-fpm" container) read and write all project files
```
setfacl -R -m user:33:rwX -m mask:rwX .
setfacl -dR -m user:33:rwX -m mask:rwX .
```

##### Allow user with UID 100 ("nginx" in "webserver" container) read files in "web" directory
```
setfacl -R -m user:100:rX ./web
setfacl -dR -m user:100:rX ./web
```

### 5. Setup the application
[Application setup guide](./installation-using-docker-application-setup.md)
