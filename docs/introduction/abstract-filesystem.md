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
For more information how to set up Flysystem with WYSIWYG visit [FMElfinderBundle Documentation](https://github.com/helios-ag/FMElfinderBundle/blob/8.0/Resources/doc/flysystem.md).

#### Create Nginx proxy to load files from different storage
If you changed the file storage, you have to change also loading of these files to be accessible from the frontend of your application.
You need to update your Nginx proxy to access your new storage.

### The Inevitable Exceptions
In some cases, you need to download/upload files to your local filesystem, do some job with them and then upload the result via the abstract filesystem.
