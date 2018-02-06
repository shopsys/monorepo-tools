# Installation Using Docker for Windows 10 Home, 8, 8.1, 7
*Virtualization technology (e.g. Docker, Vagrant) is generally significantly slower on Windows than on UNIX operating systems. Running Shopsys Framework on Windows via Docker can cause performance issues such as page load taking a few seconds (~4s on Windows, ~0,5s on Linux or Mac OS).*

## Supported systems
- Windows 10 Home
- Windows 8, 8.1
- Windows 7 

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [Docker Toolbox on Windows](https://docs.docker.com/toolbox/toolbox_install_windows/)

## Steps

### 1. Download the Shopsys Framework sources
Docker Toolbox for Windows mounts C:/Users to Docker containers on default. 
It means that any directory under C:/Users (e.g. C:/Users/<user_name>/shopsys-framework) will work automatically.
If you want to store your folder in other scope [see for example this article](https://gist.github.com/matthiasg/76dd03926d095db08745).

```
git clone https://git.shopsys-framework.com/shopsys/shopsys-framework.git
cd shopsys-framework
```

### 2. Create docker-compose.yml file
Create `docker-compose.yml` from template [`docker-compose.yml.dist`](../../docker/conf/docker-compose.yml.dist).

```
cp docker/conf/docker-compose.yml.dist docker-compose.yml
```

### 3. Compose Docker container
Run `Docker Quickstart Terminal` as administrator, then execute:
```
docker-compose up -d
```

### 4. Grant system users inside the container the required permissions
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
