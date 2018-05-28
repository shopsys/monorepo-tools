# Installation Using Docker for Windows 10 Pro and higher
*Virtualization technology (e.g. Docker, Vagrant) is generally significantly slower on Windows than on UNIX operating systems. Running Shopsys Framework on Windows via Docker can cause performance issues such as page load taking a few seconds (~4s on Windows, ~0,5s on Linux or Mac OS).*

## Supported systems
- Windows 10 Pro
- Windows 10 Enterprise
- Windows 10 Education

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PHP](http://php.net/manual/en/install.windows.php)
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-windows)
* [Docker for Windows](https://docs.docker.com/docker-for-windows/install/)

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
Create `docker-compose.yml` from template [`docker-compose-win.yml.dist`](../../project-base/docker/conf/docker-compose-win.yml.dist).

```
copy docker\conf\docker-compose-win.yml.dist docker-compose.yml
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
[Application setup guide](installation-using-docker-application-setup.md)
