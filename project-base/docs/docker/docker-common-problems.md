# Docker common problems solutions
If you are developing on Shopsys Framework using docker, you might run into some problems during the process.

Most of the time you might think that a problem is in docker, but the truth is that you are probably using it wrong. This document
provides advices that will help you develop Shopsys Framework on docker without problems.

## Multiple Projects ran by docker
If you are using Shopsys for more than one project, you might run into a problem with container names and their ports.
Docker requires to have unique container name and port for each container and since our `docker-compose` is not dynamically initialized,
it contains hard coded container names and ports and that makes running more projects in docker on same machine impossible without 
modifying your configuration.

With that being said we got 2 options to solve this problem. First is more simple and is used if we only need  one project running at the time. 
Second solution is more viable for someone who really needs to have projects ready to run in a few seconds and often end up having 
2 or more projects running at the same time.

### Only one project running at the time
All we really need to do is to properly turn off `docker-compose`. 

Usually everyone shut off their `docker-compose` by running `docker-compose stop`, which is not correct way.

This command is used to stop containers, not to delete them. That means that if you now try to start docker compose
in other project, it will output error that there already are containers with that names. 
That's true because these stopped containers are still registered in memory.

To properly delete your workspace containers, run:

```
docker-compose down
```

This will not only stop the containers but it will also delete them. This means, that containers and all their data in volumes will be deleted.
Now you can use same configuration in other project and it will work.

### Proper Solution
So what if we don't want to always reinstall whole containers and we want our data to persist in volumes?

Earlier we said that Docker needs to have unique container names and ports. 

So how about changing their name?
We recommend to replace `shopsys-framework` with your project name for instance php-fpm conainer that is defaultly named as 
`shopsys-framework-php-fpm` would now be named `my-project-name-php-fpm`.

This would actually work only if you always downed `docker-compose` before switching between projects.
Because it would try to locate our localhost ports to the same value and that would fail.

So we need to change the ports of the containers. Containers have their ports defined in this format

```
8000:8000
``` 

First one defines port exposed on our local computer, second one is for docker network. Since with every start of 
docker compose docker creates the new network and that isolates each project from each other, we do not need to care about second port.
We actually just need to allocate the first port to free port on our local system.

Since we are trying to change ports on your local machine there is a chance that you will pick port that is already allocated for something else running on your computer.
You can check all of your taken ports using `netstat` (for MacOs `lsof`).

```
netstat -ltn
```

This will output all listening TCP ports in numeric format. Now we can just pick one that isn't in this list and set it to our container.

*Note: Try not to use ports between 1000-1100, these are ports that root usually uses for its processes.*

So now we got configured our `docker-compose` files in a way they do not have any conflicts among them.
That way we can have as many projects running at the same time as many ports there are in our local network.

Remember that after changing these you need to do few things differently.
* You changed `port` of webserver container which affects the domain URL, so you need to change ports in `domains_urls.yml`.
* You changed `container_name` of php-fpm which means that in order to get inside the php-fpm container you must now use this name.
  for instance, if your new container name is `my-new-project-name-php-fpm` you need to execute

```
docker exec -it my-new-project-name-php-fpm bash
``` 

## Update of Dockerfile
Sometimes there is need to change the dockerfile for one of our images.
If we already had project once running in docker, there is probably cached image for the container.

That means that docker does not really check if there is change in the dockerfile, 
it will always build container by cached image if it has one available.
So what we actually only need is to delete the cache of image that we changed.
Lets say that we modified our docker file for container `php-fpm`.
First we need to delete our containers in `docker-compose` because we can not delete image that is used by container that is in memory.

```
docker-compose down
```

Than we need to find id of this image by using

```
docker images
```

This will output all images that were build on our system. We are looking for image that contains our directory name and container name.
For me, my directory is `shopsys-framework` and i need to rebuild `php-fpm` image.

`shopsysframework_php-fpm` is the image that i need to rebuild. So I need to copy image_id of image which in my case is
`ea1d31c585b3` and just execute this command

```
docker rmi ea1d31c585b3
```

Now if I execute `docker-compose up -d` docker will try to find the cached build of image, but since we deleted it, 
it is forced to look right into dockerfile and build it from scratch.

Done, our change is running and our whale is floating in ocean of containers once again.

## Update of Docker-compose
Docker compose is much easier to change than images. If we change anything in `docker-compose` we just need to recreate `docker-compose`.
That is done by executing:

```
docker-compose up -d --force-recreate
```

## Docker Sync does not synchronize file consistently
Docker sync sometimes stops the synchronization of files. This is issue that docker sync is aware of, but there is no solution yet.
So if this happens, we use this workaround to get docker sync to functional state back again.

First, stop the docker compose

```
docker-compose stop
```

Then stop docker sync
```
docker-sync stop
```

Find the docker sync container, copy container_id of it and delete the docker sync container:

```
docker ps -a
```

```
docker rm <container_id>
```

Then start docker sync and docker compose:

```
docker-sync start
```

```
docker-compose up -d
```

This is actually a way to properly restart docker sync.
