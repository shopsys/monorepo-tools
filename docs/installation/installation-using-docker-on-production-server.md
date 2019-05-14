# Installation Using Docker on Production Server

**This guide is for the version which is not released yet. See the [version for `v7.1.0`](https://github.com/shopsys/shopsys/blob/v7.1.0/docs/installation/installation-using-docker-on-production-server.md).**

This guide shows you how to install and configure production server applications needed for your project based on [Shopsys Framework](https://github.com/shopsys/project-base).  
We do not want to setup each application manually and we want to have separate runtime for each one.
We use docker containers, built from docker images and php source code from git repository to have everything setup correctly and fast.
As we do not want to lose data after deploying a new version of the project, we install all the data storages (postgres, elasticsearch, redis) natively.
This guide also shows you how to setup first built image of the project on production server and how to deploy new versions of the project.  

## Server Setup

CentOS is very common OS for production servers so we use it on our production server.

### Docker

First we install Docker using [installation guide](https://docs.docker.com/install/linux/docker-ce/centos/#set-up-the-repository).  
We want docker to start after server reboot using [postinstallation guide](https://docs.docker.com/install/linux/linux-postinstall/#configure-docker-to-start-on-boot).  
Then we install docker-compose using [installation guide](https://docs.docker.com/compose/install/#prerequisites).

### Firewall

It is very important to have our containers inaccessible from outside.
For that purpose we need to update `firewalld` configuration with these commands,
because Docker overrides `firewalld` and publishes ports on the server by default.
```
firewall-cmd --permanent --direct --add-chain ipv4 filter DOCKER
firewall-cmd --permanent --direct --add-rule ipv4 filter DOCKER 0 ! -s 127.0.0.1 -j RETURN
```

### Nginx

Let's presume that we want to have our site running on `HTTPS` protocol and everything that concerns domain and its `certificates` is already setup.
[Nginx atricle](https://www.nginx.com/blog/nginx-https-101-ssl-basics-getting-started/#HTTPS) provides us with some helpful information about the setup.
Only thing that is missing is to connect the domain to application that runs in docker containers via port 8000 on 127.0.0.1 ip address.
First we need to allow Nginx to connect to sockets by executing a command in shell console.
 ```
setsebool httpd_can_network_connect on -P
```
Then we add location block into `/etc/nginx/conf.d/<YOUR_DOMAIN_HERE>.conf` into server block so the config looks like this.
```
server {
    listen 443 http2 ssl;

    server_name <YOUR_DOMAIN_HERE>;

    root /usr/share/nginx/html;

    ssl_certificate /etc/ssl/linux_cert+ca.pem;
    ssl_certificate_key /etc/ssl/<YOUR_DOMAIN_HERE>.key;

    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;
    ssl_ciphers 'EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH';

    gzip on;
    gzip_proxied any;
    gzip_types
        text/css
        text/javascript
        text/xml
        text/plain
        application/javascript
        application/x-javascript
        application/json;

    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $https;
        proxy_pass http://127.0.0.1:8000;
        # display maintenance page if docker app is not available
        error_page 502 =503 @maintenance;
   }

   location @maintenance {
        add_header cache-control "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate";
        try_files /maintenance.html /maintenance.html;
   }
}

server {
    listen 80;
    server_name <YOUR_DOMAIN_HERE>;

    return 301 https://<YOUR_DOMAIN_HERE>$request_uri;
}
```
We update configuration of natively installed Nginx.
```
service nginx restart
```
Maintenance page [`maintenance.html`](../../project-base/app/maintenance.html) will be exported into `/usr/share/nginx/html` from `php-fpm` container later.

### Database

We need PostgresSQL in version 10.5 installed. To get this done we need to add repository to our server and then install PostgresSQL server with PostgresSQL client.
```
rpm -Uvh https://yum.postgresql.org/10/redhat/rhel-7-x86_64/pgdg-centos10-10-2.noarch.rpm
yum install postgresql10-server postgresql10 postgresql10-contrib
```
Next we initialise PostgresSQL database.
 ```
/usr/pgsql-10/bin/postgresql-10-setup initdb
```
We need to allow access to database from docker network that will operate on 192.168.0.1 subnet by adding one line into the config file.
```
echo host all all 192.168.0.1/16 md5 >> /var/lib/pgsql/10/data/pg_hba.conf
```
We edit configuration file `/var/lib/pgsql/10/data/postgresql.conf` of postgresql to match application needs based on our [postgres.conf](../../project-base/docker/postgres/postgres.conf).
We also allow to establish connection via localhost and 192.168.0.1 subnet by modifying one line in `postgresql.conf`.
```
listen_addresses = '0.0.0.0'
```
Now we register and launch PostgresSQL server as a service.
```
systemctl start postgresql-10
systemctl enable postgresql-10
```
Next with help of default postgres administration user we create new database user with login root. You will be prompted to enter password for newly created user root.
```
sudo -u postgres createuser --createdb --superuser --pwprompt root
```
Now we need to allow connection between docker containers and database via local network and PostgresSQL port.
```
cat <<EOT > /etc/firewalld/services/postgresql.xml
<?xml version="1.0" encoding="utf-8"?>
<service>
  <short>PostgreSQL</short>
  <description>PostgreSQL Database Server</description>
  <port protocol="tcp" port="5432"/>
  <destination ipv4="192.168.0.1/16"/>
</service>
EOT
firewall-cmd --permanent --zone=public --add-service=postgresql
firewall-cmd --reload
```

### Redis 4.0

For storing cache and sessions we need to [install](https://redis.io/download#installation) Redis server.
Also we want it running as a service.
```
<REDIS_DIRECTORY>/utils/install_server.sh
```
In addition we want redis server to operate also on 192.168.0.1 subnet so we modify one line in configuration file that is set by default in folder `/etc/redis/`.
```
bind 0.0.0.0
```
After configuration change, configuration need to be reloaded by service restart.
```
service redis_6379 restart
```
Now we just need to allow communication between docker containers and Redis server.
```
cat <<EOT > /etc/firewalld/services/redis.xml
<?xml version="1.0" encoding="utf-8"?>
<service>
  <short>Redis</short>
  <description>Cache tool.</description>
  <port protocol="tcp" port="6379"/>
  <destination ipv4="192.168.0.1/16"/>
</service>
EOT
firewall-cmd --permanent --zone=public --add-service=redis
firewall-cmd --reload
```

If you are using multiple Shopsys Framework instances on same machine, you might want to prefix your entries names in Redis.
You can do that by setting environment variable `REDIS_PREFIX` in `docker-compose.yml` file for `php-fpm` service.

### Elasticsearch

First we need to install Java SDK environment.
```
yum install java-1.8.0-openjdk
```

Next we [install](https://www.elastic.co/guide/en/elasticsearch/reference/current/rpm.html) elasticsearch and allow connecting to it via local network.
```
cat <<EOT > /etc/firewalld/services/elasticsearch.xml
<?xml version="1.0" encoding="utf-8"?>
<service>
  <short>Elasticsearch</short>
  <description>Elasticsearch is a distributed, open source search and analytics engine, designed for horizontal scalability, reliability, and easy management.</description>
  <port protocol="tcp" port="9300"/>
  <port protocol="tcp" port="9200"/>
  <destination ipv4="192.168.0.0/16"/>
</service>
EOT
firewall-cmd --permanent --zone=public --add-service=elasticsearch
firewall-cmd --reload
```
We will also make elasticsearch server listen on 192.168.0.1 subnet by modifying one line in `/etc/elasticsearch/elasticsearch.yml`.
```
network.host: 0.0.0.0
```
We also need to restart service so the new configuration is applied.
```
service elasticsearch restart
```

## Deployment On Production Server

Our server is setup for production so we want to be able to deploy changes that we made in git repository of the project to production server.  
First we need to build image of the project and then deploy it as `php-fpm` docker container.

### Docker Image Building

We can do the whole process manually of write the commands into some deployment application that can help with the automation.
We need to clone project repository with the specific tag or commit hash into some workspace.  
```
git clone <YOUR_PROJECT_REPOSITORY> (e.g. https://github.com/shopsys/project-base.git)
cd project-base
git checkout $commit_hash
```
Then we setup environment for building the image with the correct data for production server.
Now we create configuration file for domains.
```
echo $'domains:
    -   id: 1
        name: <YOUR_DOMAIN_NAME_HERE>
        locale: en
' > app/config/domains.yml
```
For each domain we need to create config with domain url.
```
echo $'domains_urls:
    -   id: 1
        url: https://<YOUR_DOMAIN_HERE>
' >  app/config/domains_urls.yml
```
Then we check whether `mailer_master_email_address` property in [`parameters.yml.dist`](../../project-base/app/config/parameters.yml.dist) is set correctly.

After the project is setup correctly, we launch the build of php-fpm container by docker build command that will build image with composer, npm packages and created assets.
```
docker build \
    -f ./docker/php-fpm/Dockerfile \
    --target production \
    -t production-php-fpm \
    --compress \
    .
```
With `f` parameter we set path to Dockerfile that builds image.
With `t` parameter we set the name of built image.

***Note:** During the build of `production target`, there will be installed 3-rd party software as dependencies of Shopsys Framework by [Dockerfile](https://docs.docker.com/engine/reference/builder/), [composer](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies) and [npm](https://docs.npmjs.com/about-the-public-npm-registry) with licenses that are described in document [Open Source License Acknowledgements and Third-Party Copyrights](../../open-source-license-acknowledgements-and-third-party-copyrights.md)*

If we are building the image on different server than production server, we can push built image into docker registry of production server via ssh.
We use `-oStrictHostKeyChecking=no` argument to have ssh connection without the prompt that asks about adding target server record into `known_hosts` ssh configuration.
We also want to establish connection to the server without prompting for password so we will use [key exchange method](http://sshkeychain.sourceforge.net/mirrors/SSH-with-Keys-HOWTO/SSH-with-Keys-HOWTO-4.html).
Before uploading the built image we perform cleanse of old images from the registry.
```
ssh -oStrictHostKeyChecking=no -i <PRIVATE_KEY_PATH> root@<YOUR_DOMAIN_HERE> docker image prune -f
```
Then we can upload built image into registry of our server.
```
docker save production-php-fpm | gzip | ssh -oStrictHostKeyChecking=no -i <PRIVATE_KEY_PATH> root@<YOUR_DOMAIN_HERE> 'gunzip | docker load'
```

### First Setup Deploy

We have setup server and also built image from project git repository in the server docker registry so we are now able to deploy application and setup it with base data.

We log into the server using ssh.  
Now we need to copy [`docker-compose-prod-deploy.yml.dist`](../../project-base/docker/conf/docker-compose.prod.yml.dist) into folder on the production server as `docker-compose.yml`.  
After the image is in the registry of the production server we create docker containers and build application for production with clean DB and base data.  
We use parameter `-p` to specify the name of the project and prefix for the volumes so these will be easily accessible.
There are named volumes created under path `/var/lib/docker/volumes/` and one persisted folder `production-content` for all uploaded images and generated files that should not be removed.  
We create persisted folder with correct owner id `33` so internal docker `php-fpm` container user has access into the folder.
```
mkdir /var/www/production-content
chown -R 33:33 /var/www/production-content
```
and start containers with docker-compose.
```
cd <PROJECT_ROOT_PATH> #(e.g. /var/www/html)
docker-compose -p production up -d
```
Now we export maintenance page from `php-fpm` container.
```
docker cp  production-php-fpm:/var/www/html/app/maintenance.html /usr/share/nginx/html
```
Then we create database and build the application.
```
docker-compose -p production exec php-fpm ./phing db-create build-new
```
*Note: In this step you were using multiple Phing targets.
More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

***Note:** During the execution of `build-new target` there will be installed 3-rd party software as dependencies of Shopsys Framework by [composer](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies) and [npm](https://docs.npmjs.com/about-the-public-npm-registry) with licenses that are described in document [Open Source License Acknowledgements and Third-Party Copyrights](../../open-source-license-acknowledgements-and-third-party-copyrights.md)*


Now the application should be running.
We want to setup scheduler for execution of cron jobs by adding one line into `/etc/crontab` file.
Cron job is executed every 5 minutes in `php-fpm` container under `root` user privileges.
```
*/5 * * * * root /usr/bin/docker exec production-php-fpm php phing cron
```
Since web application is running we can go to administration and change passwords for default administrators.
With login `superadmin` and password `admin123` we can do it via these urls:
* `/admin/administrator/edit/1`
* `/admin/administrator/edit/2`

Now we go to `/admin/dashboard/` and fulfill all requests that are demanding for us by red colored links.

### Next Deploys

We have running production shop project and we want to update it with some changes that were made in the project git repository.
We need to follow some steps that will change old version of the shop for the new one.

To preserve created data we need to use phing target `build-deploy-part-2-db-dependent` for building application environment of `php-fpm` container, maintenance page is needed if there exist unapplied database migrations.

With each update of master branch in our repository we need to rebuild image based on [Docker Image Building](./installation-using-docker-on-production-server.md#docker-image-building) section.

We log into the server using ssh.  
Now we are logged in production server and we start to deploy newly built production image.
```
cd <PROJECT_ROOT_PATH> (e.g. /var/www/html)

# make sure that container for building the application image is cleared
docker rm -f build-php-fpm-container

# launch container for building the application image
docker run --detach --name build-php-fpm-container \
    --add-host redis:192.168.0.1 --add-host postgres:192.168.0.1 --add-host elasticsearch:192.168.0.1 --add-host smtp-server:192.168.0.1 \
    --network production_shopsys-network \
    production-php-fpm \
    php-fpm

# turn on maintenance page on actual running production container
docker exec production-php-fpm php phing maintenance-on

# launch build part2 that could have impact on actual running production container
docker exec build-php-fpm-container php phing build-deploy-part-2-db-dependent

# save the current state of container into the image production-php-fpm that is refered in php-fpm service of docker-compose.yml file
docker commit build-php-fpm-container production-php-fpm

# remove actual running production container with the web volume that is shared with webserver container
docker-compose -p production rm -fs php-fpm webserver
docker volume rm -f production_web-volume

# recreate production container with webserver container with new web folder
docker-compose -p production up -d --force-recreate webserver

# remove container for building the application image
docker rm -f build-php-fpm-container
```

## Logging
If you need to inspect your application logs, use `docker-compose logs` command.
For more information about logging see [the separate article](/docs/introduction/logging.md).

## Conclusion

Now we have running project based on [Shopsys Framework](https://github.com/shopsys/project-base) docker containers.
We know how to deploy changes that were made into project git repository.
We have setup server with natively installed applications for storing persisted data on production server so there is no risk of loosing data with new deploys of the project.
