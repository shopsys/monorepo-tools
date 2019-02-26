# Releasing a new version of Shopsys Framework

For releasing a new version of Shopsys Framework, we are leveraging `release` command from [symplify/monorepo-builder](https://github.com/Symplify/MonorepoBuilder) package.

All the source codes and configuration of our release process can be found in `utils/releaser` folder that is located in the root of the monorepo.

Each step of the release process is defined as an implementation of `Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface`,
therefore we refer to the step definitions as to "release workers".

## Stages

The whole release process is divided into 3 stages that are run separately:

1. `release-candidate`
    - steps that are done before the release candidate branch is sent to code review and testing
    - the release workers are defined in `src/ReleaseWorker/ReleaseCandidate` folder
1. `release`
    - steps that are done during the actual release
    - the release workers are defined in `src/ReleaseWorker/Release` folder
1. `after-release`
    - steps that are done after the release
    - the release workers are defined in `src/ReleaseWorker/AfterRelease` folder


## Release command

**Important note:** Before you start releasing, you need to mount your `.gitconfig` to `php-fpm` docker container
to be able to perform automated commits within the container.
Add following line into your `docker-compose.yml` in `services -> php-fpm -> volumes` path:
```yaml
- ~/.gitconfig:/home/www-data/.gitconfig
```

To perform a desired stage, run the following command in the `php-fpm` docker container and follow instructions that you'll be asked in console.
```
vendor/bin/monorepo-builder release <release-number> --stage <stage> -v
```
If you want only to display a particular stage, along with the release worker class names, add the `--dry-run` argument:
```
vendor/bin/monorepo-builder release <release-number> --dry-run --stage <stage> -v
```

### Notes
- The "release-number" argument is the desired tag you want to release, it should always follow [the semantic versioning](https://semver.org/)
and start with the "v" prefix, e.g. `v7.0.0-beta5`.
- The releaser needs `.git` folder available - this is a problem currently for our Docker on Mac and Windows configuration
as the folder is currently ignored for performance reasons.
There is [an issue](https://github.com/shopsys/shopsys/issues/536) on Github that mentions the problem.
However, there is a workaround - you can add new `docker-sync` volume just for git.
- Releasing a stage is a continuously running process so do not exit your CLI if it is not necessary.
- When updating mutual dependencies in shopsys packages, the dependency on `coding-standards` is kept untouched in `http-smoke-testing` package
as it is dependent on an obsolete version of the `coding-standards` package. See `Shopsys\Releaser\DependencyUpdater`.
This behaviour should be discarded once the `http-smoke-testing` is dependent on `dev-master` version of `coding-standards` like all the other Shopsys packages.
