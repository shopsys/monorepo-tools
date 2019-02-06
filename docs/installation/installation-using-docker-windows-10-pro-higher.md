# Installation Using Docker for Windows 10 Pro and higher

**Expected installation time:** 3 hours.

This guide covers building new projects based on Shopsys Framework.
If you want to contribute to the framework itself,
you need to install the whole [shopsys/shopsys](https://github.com/shopsys/shopsys) monorepo.
Take a look at the article about [Monorepo](../introduction/monorepo.md) for more information.

This solution uses [*docker-sync*](http://docker-sync.io/) (for fast two-way synchronization of the application files between the host machine and Docker volume).

## Supported systems
- Windows 10 Pro
- Windows 10 Enterprise
- Windows 10 Education

## Requirements
* [GIT](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PHP](http://php.net/manual/en/install.windows.php)
* [Docker for Windows](https://docs.docker.com/docker-for-windows/install/)
    * Docker for Windows requires at least 4 GB of memory, otherwise, `composer install` can result in `Killed` status (we recommend to set 2 GB RAM, 1 CPU and 2 GB Swap in `Docker -> Preferences… -> Advanced`)
* [Docker-sync](http://docker-sync.io/) (installation guide [see below](./installation-using-docker-windows-10-pro-higher.md/#installation-of-docker-sync-for-windows))

### Installation of Docker-sync for Windows

***Note:** be aware of using custom firewalls or protection tools other than default `Windows Defender`, we experienced that some of them make synchronization malfunctioning because of blocking synchronization ports. To speed up the synchronization and developing faster, you can exclude folder from indexing and search path of `Windows Defender`.*
* In settings of Windows docker check `Expose daemon on localhost:2375` in `General` tab and check drive option in `Shared Drives` tab, where the project will be installed, you will be prompted for your Windows credentials.
* Enable WSL - Open the `Windows Control Panel`, `Programs and Features`, click on the left on `Turn Windows features on or off` and check `Windows Subsystem for Linux` near the bottom, restart of Windows is required.
* Install `Debian` app form `Microsoft Store` and launch it, so console window is opened.
* Execute following commands in console window.
    Update linux packages so system will be up to date to install packages needed for running docker-sync.
    ```
    sudo apt update
    ```

    Now install the tools needed for adding package repositories from which the system will be able to download docker, docker-sync and unison synchronization strategy driver.
    ```
    sudo apt install -y --no-install-recommends apt-transport-https ca-certificates curl gnupg2 software-properties-common
    ```

    Add repository for docker, then install it and configure environment variable for connecting to Windows docker.
    ```
    curl -fsSL https://download.docker.com/linux/debian/gpg | sudo apt-key add -
    sudo apt-key fingerprint 0EBFCD88
    sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/debian $(lsb_release -cs) stable"
    sudo apt update
    sudo apt install -y --no-install-recommends docker-ce
    echo "export DOCKER_HOST=tcp://127.0.0.1:2375" >> ~/.bashrc && source ~/.bashrc
    ```

    Install docker-compose tool that will help us to launch containers via `docker-compose.yml` configuration file.
    ```
    sudo curl -L "https://github.com/docker/compose/releases/download/1.22.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    ```

    Install ruby as installation tool for docker-sync in specific version for working unison synchronization strategy driver.
    ```
    sudo apt install -y --no-install-recommends ruby ruby-dev
    sudo gem install docker-sync -v 0.5.7
    ```

    Download, compile and install unison driver.
    ```
    sudo apt install -y --no-install-recommends build-essential make
    wget -qO- http://caml.inria.fr/pub/distrib/ocaml-4.06/ocaml-4.06.0.tar.gz | tar xvz
    cd ocaml-4.06.0
    ./configure
    make world opt
    umask 022
    sudo make install clean
    wget -qO- https://github.com/bcpierce00/unison/archive/v2.51.2.tar.gz | tar xvz
    cd unison-2.51.2
    make UISTYLE=text
    sudo cp src/unison /usr/local/bin/unison
    sudo cp src/unison-fsmonitor /usr/local/bin/unison-fsmonitor

    # remove sources of sync tools
    cd ../..
    rm -rf ocaml-4.06.0 *.tar.gz
    ```

    Set timezone of the sytem as docker-sync requirement.
    ```
    sudo dpkg-reconfigure tzdata
    ```

    Set WSL init script for mounting of computer drives from root path `/` instead of `/mnt` path.
    ```
    echo -e [automount]\\nenabled = true\\nroot = /\\noptions = \"metadata,umask=22,fmask=11\" | sudo dd of=/etc/wsl.conf
    ```

    Add valid debian repository for php version that is used by composer and install composer.
    ```
    sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list

    sudo apt update
    sudo apt install -y --no-install-recommends composer git
    ```

    Close console window and open it again so the new configuration is loaded.

## Shopsys Framework Installation
### 1. Create new project from Shopsys Framework sources
After WSL installation use linux console for each command.  
Pick path of some directory in Windows filesystem and move into it, for example documents of windows user `myuser` so path will be like this.
```
cd /c/Users/myuser/Documents/
```

Install project with composer.
```
composer create-project shopsys/project-base --stability=beta --no-install --keep-vcs
cd project-base
```

*Notes:*
- *The `--no-install` option disables installation of the vendors - this will be done later in the Docker container.*
- *The `--keep-vcs` option initializes GIT repository in your project folder that is needed for diff commands of the application build and keeps the GIT history of `shopsys/project-base`.*
- *The `--stability=beta` option enables you to install the project from the last beta release. Default value for the option is `stable` but there is no stable release yet.*

### 2.1 Use install script
In case you want to start demo of the app as fast as possible, you can now execute install script.

```
./scripts/install.sh
```

If you want to know more about what is happening during installation, continue with next step.

### 2.2 Create docker-compose.yml and docker-sync.yml file
Create `docker-compose.yml` from template [`docker-compose-win.yml.dist`](../../project-base/docker/conf/docker-compose-win.yml.dist).
```
cp docker/conf/docker-compose-win.yml.dist docker-compose.yml
```

Create `docker-sync.yml` from template [`docker-sync-win.yml.dist`](../../project-base/docker/conf/docker-sync-win.yml.dist).
```
cp docker/conf/docker-sync-win.yml.dist docker-sync.yml
```

### 3. Compose Docker container
On Windows you need to synchronize folders using docker-sync.
Before starting synchronization you need to create a directory for persisting Vendor data so you won't lose it when the container is shut down.
```
mkdir -p vendor
docker-sync start
```

Then rebuild and start containers
```
docker-compose up -d
```

***Note:** During installation there will be installed 3-rd party software as dependencies of Shopsys Framework by [Dockerfile](https://docs.docker.com/engine/reference/builder/) with licenses that are described in document [Open Source License Acknowledgements and Third-Party Copyrights](../../open-source-license-acknowledgements-and-third-party-copyrights.md)*

### 5. Setup the application
[Application setup guide](installation-using-docker-application-setup.md)
