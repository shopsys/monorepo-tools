# Installation Using Docker

This guide shows how to use prepared Docker Compose configuration to simplify the installation process.
You do not need to install and configure the whole server stack (Nginx, PostgreSQL, etc.) in order to run and develop Shopsys Framework on your machine.

## How it works
All the services needed by Shopsys Framework like Nginx or PostgreSQL are run in Docker.
Your source code is automatically synchronized between your local machine and Docker container in both ways.

That means that you can normally use your IDE to edit the code while it is running inside a Docker container.

## Supported platforms
This solution uses [*docker-sync*](http://docker-sync.io/) (for fast two-way synchronization of the application files between the host machine and Docker volume) that currently works on:
* Linux
* Mac OS X

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

### 4. Setup the application
Now that the Docker environment is prepared we can setup the application itself.

#### 4.1. Connect into terminal of the Docker container
```
docker exec -it shopsys-framework-php-fpm bash
```

#### 4.2. Install dependencies and configure parameters
```
composer install
```

Composer will prompt you to set parameters ([description of parameters](installation-guide.md#2-install-dependencies-and-configure-parameters)):

Important parameters to set for `app/config/parameters.yml` are listed bellow (the others can be set to default - just press Enter):

| parameter name    | parameter value |
| ----------------- | --------------- |
| database_host     | postgres        |
| database_port     | 5432            |
| database_name     | shopsys         |
| database_user     | root            |
| database_password | root            |
| ...               | ...             |
| mailer_host       | smtp-server     |

Important parameters to set for `app/config/parameters_test.yml` are listed bellow (the others can be set to default - just press Enter):

| parameter name         | parameter value |
| ---------------------- | --------------- |
| test_database_host     | postgres        |
| test_database_port     | 5432            |
| test_database_name     | shopsys-test    |
| test_database_user     | root            |
| test_database_password | root            |

For development choose `n` when asked `Build in production environment? (Y/n)`.

It will set the environment in your application to `dev` (this will, for example, show Symfony Web Debug Toolbar).

#### 4.2. Configure domains
Create `domains_urls.yml` from `domains_urls.yml.dist`.

```
cp app/config/domains_urls.yml.dist app/config/domains_urls.yml
```

#### 4.3. Create databases
```
./phing db-create
./phing test-db-create
```

#### 4.4. Build the application
```
./phing build-demo-dev
./phing img-demo
```

### 5. See it in your browser!
Open [http://127.0.0.1:8000/](http://127.0.0.1:8000/) to see running application.

You can also login into the administration section on [http://127.0.0.1:8000/admin/](http://127.0.0.1:8000/admin/) with default credentials:
* Username: `admin` or `superadmin` (the latter has access to advanced options)
* Password: `admin123`

You can also manage the application database using [Adminer](https://www.adminer.org) by going to [http://127.0.0.0:1000](http://127.0.0.0:1000).