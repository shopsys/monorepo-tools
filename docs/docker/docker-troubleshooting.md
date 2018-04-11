# Docker Troubleshooting

1. [How to Run Multiple Projects by Docker](#how-to-run-multiple-projects-by-docker)
    1. [Multiple Projects - Quick Solution - Only One Project Running at the Time](#multiple-projects---quick-solution---only-one-project-running-at-the-time)
    1. [Multiple Projects - Long Term Solution](#multiple-projects---long-term-solution)
1. [Update of Dockerfile is not Reflected](#update-of-dockerfile-is-not-reflected)
1. [Update of Docker-compose is not Reflected](#update-of-docker-compose-is-not-reflected)
1. [Docker Sync does not Synchronize File Consistently](#docker-sync-does-not-synchronize-file-consistently)

If you are developing on Shopsys Framework using docker, you might run into some problems during the process.

Most of the time you might think that a problem is in docker, but the truth is that you are probably using it wrong. This document
provides advices that will help you develop Shopsys Framework on docker without problems.

## How to Run Multiple Projects by Docker
If you are using docker for more than one Shopsys Framework project, you might run into a problem with container names and their ports.
Docker requires to have unique container name and port for each container and since our `docker-compose` is not dynamically initialized,
it contains hard coded container names and ports and that makes running more projects in docker on same machine impossible without 
modifying your configuration.

With that being said we got two options to solve this problem.

### Multiple Projects - Quick Solution - Only One Project Running at the Time
This solution is simpler and is used if we only need one project running at the time.

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

### Multiple Projects - Long Term Solution
This solution is more viable for someone who really needs to have projects ready to run in a few seconds and often end up having
two or more projects running at the same time. So what if we don't want to always reinstall whole containers and we want our data to persist in volumes?

Earlier we said that Docker needs to have unique container names and ports. 

So how about changing their name?
We recommend to replace `shopsys-framework` with your project name. For instance, php-fpm conainer that is defaultly named as 
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

## Update of Dockerfile is not Reflected
Sometimes there is need to change the dockerfile for one of our images.
If we already had project running once in docker, there is probably cached image for the container.

That means that docker does not really check if there is change in the dockerfile, 
it will always build container by cached image. So what we actually need is to rebuild our containers.
First we need to stop our containers in `docker-compose` because we cannot update containers that are already in use:

```
docker-compose stop
```

Then we need to force Docker to rebuild our containers:

```
docker-compose build
```

Docker has now updated our containers and we can continue as usual with:
```
docker-compose up -d
```

## Update of Docker-compose is not Reflected

Docker compose is much easier to change than images. If we change anything in `docker-compose` we just need to recreate `docker-compose`.
That is done by executing:

```
docker-compose up -d --force-recreate
```

## Docker-sync stopped to sync files
Docker-sync suggests ([in known issue](https://github.com/EugenMayer/docker-sync/issues/517)) to use Docker for Mac in version 17.09.1-ce-mac42 (21090).
This version helped most people to solve their issues with syncing.

You may sometimes encounter a sync problem even with the suggested version of Docker. In those cases, you need to recreate docker-sync containers. Here are two easy steps you have to follow:

Delete your docker-sync containers and volumes (data on your host will not be removed):
```
docker-sync clean
```
Start docker-sync so your docker-sync containers and volumes will be recreated:
```
docker-sync start
```