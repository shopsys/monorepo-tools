# Installation Using Docker - application setup

This guide expects that you have already set up your Docker environment.
If you haven't already done that check the [Installation Guide for Docker](installation-using-docker.md).

## 1. Setup the application
Now that the Docker environment is prepared we can setup the application itself.

### 1.1. Connect into terminal of the Docker container
```
docker exec -it shopsys-framework-php-fpm bash
```

### 1.2. Install dependencies and configure parameters
```
composer install
```

Composer will prompt you to set parameters ([description of parameters](installation-guide.md#2-install-dependencies-and-configure-parameters)).
The default parameters are currently set for application running in Docker so you can just use the defaults.

Only exception is the `secret` parameter - you should input a random string to be used for security purposes.
It is not necessary for development though.

For development choose `n` when asked `Build in production environment? (Y/n)`.

It will set the environment in your application to `dev` (this will, for example, show Symfony Web Debug Toolbar).

### 1.3. Configure domains
Create `domains_urls.yml` from `domains_urls.yml.dist`.

```
cp app/config/domains_urls.yml.dist app/config/domains_urls.yml
```

### 1.4. Create databases
```
./phing db-create
./phing test-db-create
```

### 1.5. Build the application
```
./phing build-demo-dev
./phing img-demo
```

## 2. See it in your browser!

### On Linux, MacOS and Windows 10 Pro and higher
Open [http://127.0.0.1:8000/](http://127.0.0.1:8000/) to see running application.

You can also login into the administration section on [http://127.0.0.1:8000/admin/](http://127.0.0.1:8000/admin/) with default credentials:
* Username: `admin` or `superadmin` (the latter has access to advanced options)
* Password: `admin123`

You can also manage the application database using [Adminer](https://www.adminer.org) by going to [http://127.0.0.1:1100](http://127.0.0.1:1100).

### On Windows 10 Home, 8, 8.1, 7 (using Docker Toolbox)
Open [http://192.168.99.100:8000/](http://192.168.99.100:8000/) to see running application.

You can also login into the administration section on [http://192.168.99.100:8000/admin/](http://192.168.99.100:8000/admin/) with default credentials:
* Username: `admin` or `superadmin` (the latter has access to advanced options)
* Password: `admin123`

You can also manage the application database using [Adminer](https://www.adminer.org) by going to [http://192.168.99.100:1100](http://192.168.99.100:1100).
