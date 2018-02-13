# Docker 

This document provides basic information how we leverage docker on Shopsys 
framework to make the work with it as easy as possible.

## 1. Problem
These days, developers use machines with different
settings and configurations to work with. Modern
platforms and frameworks have in most cases 
different software requirements, so in the final, their installation may take
several hours. Our goal is to facilitate the installation of Shopsys framework,
ideally, if the developer does not need to significantly customize 
the configuration of his machine but instead of that, he has the possibility
to install the platform immediately even with all dependencies - such
as a virtual container. As a result, the developer can start development
after a few minutes after downloading the platform.

## 2. Solution - Docker
Docker provides some resources to create isolated software containers based
on the image, that includes everything needed to run it: code, runtime, system tools,
system libraries, settings. Thanks to that, the containerized software will 
always run the same, regardless of the environment.

## 3. Shopsys-framework on Docker

#### 3.1 docker-compose.yml
[Docker-compose.yml](../../docker/conf/docker-compose.yml.dist) file contains the definitions of containers, that will be created for the installation
of the Shopsys framework platform. Containers can communicate with each other. Data need to be saved
into volumes, otherwise, these data will be lost after the container is closed. 

##### Containers
* **postgres**
* **webserver**
* **php-fpm**
* **adminer**
* **smtp-server**

##### Options
The definition of container consists of some options:
* **image** : docker file with commands of server installation
* **container_name** :  name of created container
* **working_dir** : file structure of created container
* **volumes** : location of data storage in which the data will remain even after the container is removed (see [Volumes official docs.](https://docs.docker.com/engine/admin/volumes/volumes/))
* **ports** : ports mapping, in default configuration is port 8000 mapped on port 8080 inside container
* **links** : settings with which containers can the actual container communicate. If nothing is set,
an actual container can communicate with all containers
* **environment** : environment variables, after setting they can be used throughout the container
* **depends_on** : definition of dependency on another running container

##### Volumes
The definition of volumes:
* **shopsys-framework-sync** : name of the volume
* **external: true** : flag, which indicates, that volume is already defined external
(in this case in [docker-sync.yml](../../docker-sync.yml))

#### 3.2 docker-sync.yml
[Docker-sync.yml](../../docker-sync.yml) file contains the definition of synchronization for the docker-sync tool.

#### 3.3 docker/php-fpm/Dockerfile
[Dockerfile](../../docker/php-fpm/Dockerfile) is a text document that contains all the commands a user
could call on the command line to assemble an image. It is like some
kind of recipe by which final image is cooked.

Dockerfile example command:

_FROM phpdockerio/php71-fpm:latest_
* The FROM instruction specifies the base image, from which you are building

The official list of dockerfile commands can be found on [Dockerfile reference](https://docs.docker.com/engine/reference/builder/#from).

#### 3.4 docker/php-fpm/php-ini-overrides.ini
[Php-ini-overrides.ini](../../docker/php-fpm/php-ini-overrides.ini) file contains php custom configuration which is used after installing php in new container

#### 3.5 docker/nginx/nginx.conf
[Nginx.conf](../../docker/nginx/nginx.conf) file contains Nginx configuration for new webserver container.
