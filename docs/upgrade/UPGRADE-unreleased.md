# [Upgrade from v7.1.0 to Unreleased]

This guide contains instructions to upgrade from version v7.1.0 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Application
- add `TransformString::removeDriveLetterFromPath` transformer for all absolute paths that could be based with drive letter file systems and used by `local_filesystem` service ([#942](https://github.com/shopsys/shopsys/pull/942))
    - add TransformString::removeDriveLetterFromPath into `src/Shopsys/ShopBundle/DataFixtures/Demo/ImageDataFixture.php`
        ```diff
        protected function moveFilesFromLocalFilesystemToFilesystem(string $origin, string $target)
        {
            $finder = new Finder();
            $finder->files()->in($origin);
            foreach ($finder as $file) {
        -        $filepath = $file->getPathname();
        +        $filepath = TransformString::removeDriveLetterFromPath($file->getPathname());
        ```


### Configuration
 - *(low priority)* use standard format for redis prefixes ([#928](https://github.com/shopsys/shopsys/pull/928))
    - change prefixes in `app/config/packages/snc_redis.yml` and `app/config/packages/test/snc_redis.yml`. Please find inspiration in [#928](https://github.com/shopsys/shopsys/pull/928/files)
    - once you finish this change, you still should deal with older redis cache keys that don't use new prefixes. Such keys are not removed even by `clean-redis-old`, please find and remove them manually (via console or UI)

    **Be careful, this upgrade will remove sessions**


[Upgrade from v7.1.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.1.0...HEAD
