# Abstract Filesystem
One of the goals of the Shopsys Framework is to give you tools to make your e-commerce platform scalable.
One of the requirements for scalable application is separated file storage that is accessible from all application instances.

We use abstract filesystem - [Flysystem](https://github.com/thephpleague/flysystem).

## Flysystem
[Flysystem](https://github.com/thephpleague/flysystem) allows you to easily swap out a local filesystem for a remote one like Redis, Amazon S3, Dropbox etc.

### What is Flysystem used for
In Shopsys Framework we currently use [Flysystem](https://github.com/thephpleague/flysystem) to store:
- uploaded files and images
- uploaded files and images via WYSIWYG
- generated feeds
- generated sitemaps

### How to change storage adapter for filesystem
Flysystem supports a huge number of storage adapters. You can find [full list here](https://github.com/thephpleague/flysystem#community-integrations).

If you want to change the adapter used for Filesystem you must implement factory for `FilesystemFactoryInterface` and register it in `services.yml` file under `main_filesystem` alias.

#### How to change storage adapter for WYSIWYG
WYSIWYG configuration is stored in `app/config/packages/fm_elfinder.yml` file in `fm_elfinder\instances\default\connector\roots` section.
For more information how to set up Flysystem with WYSIWYG visit [FMElfinderBundle Documentation](https://github.com/helios-ag/FMElfinderBundle/blob/9.2/docs/flysystem.md).

#### Create Nginx proxy to load files from different storage
If you changed the file storage, you have to change also loading of these files to be accessible from the frontend of your application.
You need to update your Nginx proxy to access your new storage.
For instance, you can take a look of implementation for Google Cloud Storage in [nginx.conf](/project-base/docker/nginx/google-cloud/nginx.conf)
```diff
        try_files $uri @app;
    }
+   location ~ ^/content/ {
+       resolver 8.8.8.8;
+       proxy_intercept_errors on;
+       proxy_pass https://storage.googleapis.com/{{GOOGLE_CLOUD_STORAGE_BUCKET_NAME}}/web$request_uri;
+       error_page 404 = @app;
+   }
    location ~ ^/content(-test)?/images/ {
```

### The Inevitable Exceptions
In some cases, you need to download/upload files to your local filesystem, do some job with them and then upload the result via the abstract filesystem.
