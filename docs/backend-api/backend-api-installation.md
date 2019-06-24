# Backend API Installation

*Please follow these instructions with clean git working tree (you can verify with `git status`). If the installation fails, you can easily revert all changes and start again.*

*If you installed the Shopsys Framework with docker please run all commands in the `php-fpm` docker container*

Require `shopsys/backend-api` package in the `composer.json` or run

```bash
composer require shopsys/backend-api
```

Backend API installation is fully automated, so you have to run only (you have to be in the project root directory)

```bash
./vendor/shopsys/backend-api/install/install.sh
```

If it was successful, you'll see `Backend API installation was successful!`

Update your composer because the `install.sh` added a composer dependency

```bash
composer update trikoder/oauth2-bundle
```

Run database migrations

```bash
php phing db-migrations
```

*Now you can continue with creating [OAuth client](/docs/backend-api/api-authentication-oauth2.md)*

## Troubleshooting

The automated installation is based on copying the code from `vendor/shopsys/backend-api/install` directory and on applying patches.

### Copies

Copying files can cause problems only if you already had a file with the same name in the same directory.
In such case please rename your files and run the installation again.

### Patches

Patching files can fail. It can happen if you changed something in your project and these changes are incompatible with the patch.

The installation script tries to apply patches for all involved files and applies as much as possible.
In other words the installation script doesn't stop on the first patch error.

In case of error you'll see error messages like `Patch for app/AppKernel.php cannot be applied!`.
In such case please take a look into [install directory](/packages/backend-api/install/), and apply all patches manually.
* You have to only add code into files, so copy only lines that starts with plus `+ ` symbol
  * eg. from `composer.json.patch` copy the line `"trikoder/oauth2-bundle": "^1.1",` into your `composer.json`
* The directory structure in the installation directory matches the directory structure in your project
