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

### 1. Create new project from Shopsys Framework sources
Docker Toolbox for Windows mounts C:/Users to Docker containers on default. 
It means that any directory under C:/Users (e.g. C:/Users/<user_name>/project-base) will work automatically.
If you want to store your folder in other scope [see for example this article](https://gist.github.com/matthiasg/76dd03926d095db08745).

```
composer create-project shopsys/project-base --stability=alpha --no-install --keep-vcs
cd project-base
```
Notes: 
- The `--no-install` option disables installation of the vendors - this will be done later in the Docker container
- Option `--keep-vcs` initializes GIT repository in your project folder, that is needed for diff commands of application build and keeps the GIT history of `shopsys/project-base`

### 2. Create docker-compose.yml file
Create `docker-compose.yml` from template [`docker-compose.yml.dist`](../../../project-base/docker/conf/docker-compose.yml.dist).

```
copy docker\conf\docker-compose.yml.dist docker-compose.yml
```

### 3. Compose Docker container
Run `Docker Quickstart Terminal` as administrator, then execute:
```
docker-compose up -d
```

### 4. Setup the application
[Application setup guide](./installation-using-docker-application-setup.md)
