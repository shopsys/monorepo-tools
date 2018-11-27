# Installation Using Docker - application setup

This guide expects that you have already set up your Docker environment.
If you haven't already done that check the [Installation Using Docker](installation-using-docker.md).

## 1. Setup the Shopsys Framework application
Now that the Docker environment is prepared we can setup the application itself.

### 1.1. Connect into terminal of the Docker container
```
docker exec -it shopsys-framework-php-fpm sh
```

### 1.2. Install dependencies and configure parameters
```
composer install
```

If you are installing the application in production environment, you should install composer optimized.
The optimized composer speed up your application.
```
composer install -o
```

Composer will prompt you to set parameters ([description of parameters](native-installation.md#2-install-dependencies-and-configure-parameters)).
The default parameters suggested by composer are currently set for application running in Docker so you can just use these.

Only exception is the `secret` parameter - you should input a random string to be used for security purposes.
It is not necessary for development though.

### 1.3. Create databases
```
php phing db-create
php phing test-db-create
```

*Note: In this step you were using multiple Phing targets.
More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

### 1.4. Build the application
```
php phing build-demo-dev
```

## 2. See it in your browser!

Open [http://127.0.0.1:8000/](http://127.0.0.1:8000/) to see running application.

You can also login into the administration section on [http://127.0.0.1:8000/admin/](http://127.0.0.1:8000/admin/) with default credentials:
* Username: `admin` or `superadmin` (the latter has access to advanced options)
* Password: `admin123`

You can also manage the application database using [Adminer](https://www.adminer.org) by going to [http://127.0.0.1:1100](http://127.0.0.1:1100)
and Redis storage using [Redis admin](https://github.com/ErikDubbelboer/phpRedisAdmin) by going to [http://127.0.0.1:1600](http://127.0.0.1:1600).

Elasticsearch API is available on the address [http://127.0.0.1:9200](http://127.0.0.1:9200).
You can use e.g. [Postman](https://www.getpostman.com/apps) or [Kibana](https://www.elastic.co/downloads/kibana) for Elasticseacrh management.

If you need to inspect your application logs, use `docker-compose logs` command.
For more information about logging see [the separate article](/docs/introduction/logging.md).