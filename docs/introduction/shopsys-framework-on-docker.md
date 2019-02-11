# Docker

This document provides basic information about how we leverage Docker on Shopsys
Framework to make the work with it as easy as possible.

## 1. Problem
These days, developers use machines with different
settings and configurations to work with. Modern
platforms and frameworks have in most cases
different software requirements, so their installation may take
up to several hours. Our goal is to facilitate the installation of Shopsys framework,
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

An orchestration tool can further help with managing the containers (even though you don't need it for local development).
For details, you can read the [Introduction to Kubernetes](/docs/kubernetes/introduction-to-kubernetes.md).

## 3. Shopsys-framework on Docker

#### 3.1 docker-compose.yml
[Docker-compose.yml](/project-base/docker/conf/docker-compose.yml.dist) file contains the definitions of containers that will be created for the installation
of the Shopsys framework platform. Containers can communicate with each other. Data need to be saved
into volumes, otherwise, these data will be lost after the container is closed.

##### Containers
Examples of containers that we use:
* **postgres**
* **webserver**
* **php-fpm**
* **adminer**
* **smtp-server**

##### Options
The definition of container consists of some options:
* **image**: Docker image that will be downloaded from [Docker Hub](https://hub.docker.com/) and used
* **build**: data used for building the Docker image locally
* **container_name**:  name of created container
* **working_dir**: file structure of created container
* **volumes**: location of data storage in which the data will remain even after the container is removed (see [Volumes official docs.](https://docs.docker.com/engine/admin/volumes/volumes/))
* **ports**: ports mapping, in default configuration is port 8000 mapped on port 8080 inside container
* **environment**: environment variables, after setting they can be used throughout the container

##### Volumes
The definition of volumes, example:
* **shopsys-framework-sync** : name of the volume
* **external: true** : flag, which indicates, that volume is already defined external
(in this case in [docker-sync.yml](../../project-base/docker-sync.yml))

#### 3.2 docker-sync.yml
[Docker-sync.yml](/project-base/docker/conf/docker-sync.yml.dist) file contains the definition of synchronization for the docker-sync tool (it's used for Mac only).

#### 3.3 docker/php-fpm/Dockerfile
[Dockerfile](/project-base/docker/php-fpm/Dockerfile) is a text document that contains all the commands a user
should call on the command line to assemble an image. It is like some
kind of recipe by which final image is cooked.

Dockerfile example command:
```dockerfile
FROM php:7.3-fpm-stretch
```
* The `FROM` instruction specifies the base image, from which you are building

The official list of Dockerfile commands can be found on [Dockerfile reference](https://docs.docker.com/engine/reference/builder/#from).

#### 3.4 docker/php-fpm/php-ini-overrides.ini
[Php-ini-overrides.ini](/project-base/docker/php-fpm/php-ini-overrides.ini) file contains php custom configuration which is used after installing php in a new container

#### 3.5 docker/nginx/nginx.conf
[Nginx.conf](/project-base/docker/nginx/nginx.conf) file contains Nginx configuration for new webserver container.

#### 3.6 Images Distribution
While running Shopsys Framework on docker we needed to decide which distribution will be our images running on. We use 2 types of distributions for our images.

* Debian
* Alpine Linux

Both have their advantages and drawbacks

##### Debian
###### Advantages
Debian is one of the oldest distributions, so even users without Docker experience are able to modify these images because installing tools in this distributions is easy, common and very easy to find on internet if some problem occurs.

Also, Debian supports big amount of Databases and tools used by developers and are easy to install.
###### Drawbacks
Debian image is pretty big, which is not ideal for pulling these images on Cloud Solutions because there is more resources used to pull these.

##### Alpine
###### Advantages
Alpine Linux is really small and minimalistic which means that clean installation of Alpine linux takes 6MB of memory which is great for pulling these images on Cloud where resource used for pull matters.

###### Disadvantages
Dockerfiles based from Alpine are harder to modify because it is so slim that with every technology, database or tool you want to install, you also need to install all its dependencies, making it a bit hard to find right solutions.

Alpine Linux also has longtime problem with connection to MSSQL databases, due to missing support by Microsoft drivers.

We divide images used by us into 3 types.

##### Not extended, only used (PostgreSQL, Redis, Elasticsearch)
Those images are only used as it is, we are not extending them and we are only using them as they are. In this case we choose Alpine Distribution thanks to its size.

##### Application PHP-FPM
In this case, we use Debian mainly because we suppose users to modify these images often(adding php-extensions, implementing connections to Databases). As Debian is much more easier to modify, we decided that it will be best for new users to start on Debian, and if they care about size, they can always rewrite their Dockerfiles to be use alpine image if needed. Also many of clients using Shopsys Framework often connects to MSSQL databases and we want to make it easy for them.
