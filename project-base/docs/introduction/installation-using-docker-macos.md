# Installation Using Docker for MacOS

This solution uses [*docker-sync*](http://docker-sync.io/) (for fast two-way synchronization of the application files between the host machine and Docker volume).

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [Docker](https://docs.docker.com/engine/installation/)
* [Docker-sync](http://docker-sync.io/) (install via `sudo gem install docker-sync`)

## Steps
### 1. Download the Shopsys Framework sources
```
git clone https://git.shopsys-framework.com/shopsys/shopsys-framework.git
cd shopsys-framework
```

### 2. Create docker-compose.yml file
Create `docker-compose.yml` from template [`docker-compose-mac.yml.dist`](../../docker/conf/docker-compose-mac.yml.dist).
```
cp docker/conf/docker-compose-mac.yml.dist docker-compose.yml
```

### 3. Compose Docker container
On MacOS you need to synchronize folders using docker-sync
```
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