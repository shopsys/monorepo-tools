# Installation Using Docker for Linux and MacOS

This solution uses [*docker-sync*](http://docker-sync.io/) (for fast two-way synchronization of the application files between the host machine and Docker volume).

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [Docker](https://docs.docker.com/engine/installation/)
* [Ruby](https://www.ruby-lang.org/en/downloads/) (needed by *docker-sync*; on Mac OS X Ruby is present by default)
* [Docker-sync](http://docker-sync.io/) (on Mac install via `sudo gem install docker-sync`)

## Steps
### 1. Download the Shopsys Framework sources
```
git clone https://git.shopsys-framework.com/shopsys/shopsys-framework.git
cd shopsys-framework
```

### 2. Start the Docker
#### 2.1. Start files two-way synchronization
```
docker-sync start
```

#### 2.2. Compose Docker container
```
docker-compose up -d
```

### 3. Set file permissions
#### 3.1. Grant your local user permissions (Linux only)

*Note: If you use Mac OS X skip to step 3.2.*

On Linux, `docker-sync` uses *bind mount* method for sharing files between your host machine and the Docker container.
This means that file permissions (owner, group, ...) are shared between your host filesystem and Docker container, too.
In such case, files created inside Docker container will have permissions that make sense inside the container but not on your host machine as both systems have different UIDs and GIDs.
You may not be even able to read or modify such files in your IDE.

Thankfully we can use `setfacl` in order to grant the user on your host machine permissions to all existing and even newly created files.  

##### Make the current and future project files accessible by the current user on your host system
```
sudo setfacl -R -m user:`whoami`:rwX -m mask:rwX .
sudo setfacl -dR -m user:`whoami`:rwX -m mask:rwX .
```

#### 3.2. Grant system users inside the container the required permissions
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

### 4. Setup the aplication
[Application setup guide](installation-using-docker-application-setup.md)