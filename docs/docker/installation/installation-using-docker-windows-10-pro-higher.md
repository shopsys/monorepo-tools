# Installation Using Docker for Windows 10 Pro and higher
*Virtualization technology (e.g. Docker, Vagrant) is generally significantly slower on Windows than on UNIX operating systems. Running Shopsys Framework on Windows via Docker can cause performance issues such as page load taking a few seconds (~4s on Windows, ~0,5s on Linux or Mac OS).*

## Supported systems
- Windows 10 Pro
- Windows 10 Enterprise
- Windows 10 Education

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

### 3. Grant Docker access to your files
- Right click Docker icon in your system tray and choose `Settings...`
- From left menu choose `Shared Drives`
- Set your system drive including Shopsys Framework repository as `Shared` (check the checkbox)
- Click on `Apply`
- You will be prompted for your Windows credentials

### 4. Compose Docker container
```
docker-compose up -d
```

### 5. Setup the application
[Application setup guide](./installation-using-docker-application-setup.md)
