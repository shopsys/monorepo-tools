
# Directories
This article describes how to work with directories and how to create your own directories.

## Config
Default directories that need to be created in order to app to be able to run properly are defined in framework's [`/Resources/config/directories.yml`](/packages/framework/src/Resources/config/directories.yml) and are created by symfony command `shopsys:create-directories`.

## Directories type
There are 2 types of directories that are created.

### Internal directories
Directories that are used by application and do not need to be public, typically cache or logs.

These directories are grouped under `internal_directores` in `app/config/directories.yml` file and their definition is an absolute path.

For example:
```
internal_directories:
    - '%kernel.project_dir%/var/logs'
```

### Public directories
Directories that needs to be available for public usage, for example feeds or sitemaps.

These directories are grouped under `public_directores` in `app/config/directories.yml` file and their definition is relative path to the root directory of a project.

For example:
```
public_directories:
    - '/web/content/images'
```

## Adding a new directory
In case you need to create your own directories, you can simply add them into  `app/config/directories.yml` under suitable type as an array element.

For example:

```
// app/config/directories.yml

parameters:
    public_directories:
+   - '/my/new/folder'
    internal_directories:
```
