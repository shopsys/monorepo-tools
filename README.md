# Shopsys Monorepo Tools

[![Mentioned in Awesome Monorepo](https://awesome.re/mentioned-badge.svg)](https://github.com/korfuri/awesome-monorepo)

**This package is used for splitting our monorepo and we share it with our community as it is. We do not intend to support or develop it any further. Feel free to fork it and adjust for your own need.**

Tools for building and splitting monolithic repository from existing packages.
You can read about pros and cons of monorepo approach on the [Shopsys Framework Blog](https://blog.shopsys.com/how-to-maintain-multiple-git-repositories-with-ease-61a5e17152e0).

We created these scripts because we couldn't find a tool that would keep the git history of subpackages unchanged.

You may need to update your `git` (tested on `2.16.1`).

This repository is maintained by [shopsys/shopsys](https://github.com/shopsys/shopsys) monorepo, information about changes is in [monorepo CHANGELOG.md](https://github.com/shopsys/shopsys/blob/master/CHANGELOG.md).

## Quick start

### 1. Download

First download this repository so you can use the tools (eg. into `~/monorepo-tools`).

```
git clone https://github.com/shopsys/monorepo-tools ~/monorepo-tools
```

### 2. Preparing an empty repository with added remotes

You have to create a new git repository for your monorepo and add all your existing packages as remotes.
You can add as many remotes as you want.

In this example we will prepare 3 packages from github for merging into monorepo.

```
git init
git remote add main-repository http://github.com/vendor/main-repository.git
git remote add package-alpha http://github.com/vendor/alpha.git
git remote add package-beta http://github.com/vendor/beta.git
git fetch --all --no-tags
```

### 3. Building the monorepo

Then you can build your monorepo using `monorepo_build.sh`.
Just list the names of all your previously added remotes as arguments.
Optionally you can specify a directory where the repository will be located by providing `<remote-name>:<subdirectory>`, otherwise remote name will be used.

The command will rewrite history of all mentioned repositories as if they were developed in separate subdirectories.

Only branches `master` will be merged together, other branches will be kept only from first package to avoid possible branch name conflicts.

```
~/monorepo-tools/monorepo_build.sh \
    main-repository package-alpha:packages/alpha package-beta:packages/beta
```

This may take a while, depending on the size of your repositories.

Now your `master` branch should contain all packages in separate directories. For our example it would mean:
* **main-repository/** - contains repository *vendor/main-repository*
* **packages/**
  * **alpha/** - contains repository *vendor/alpha*
  * **beta/** - contains repository *vendor/beta*

### 4. Splitting into original repositories

You should develop all your packages in this repository from now on.

When you made your changes and would like to update the original repositories use `monorepo_split.sh` with the same arguments as before.

```
~/monorepo-tools/monorepo_split.sh \
    main-repository package-alpha:packages/alpha package-beta:packages/beta
```

This will push all relevant changes into all of your remotes.
It will split and push your `master` branch along with all tags you added in this repository.
Other branches are not pushed.

It may again take a while, depending on the size of your monorepo.

***Note:***  
*The commits in the split repositories should be identical to those from the original repo, keeping the git history intact.*
*Thus, if you have checked out the original `master` previously, you should be able to fast-forward to the new version after splitting.*  
*The only known exception is a signed commit (note that GitHub signs commits made via its web UI by default).*
*If you have signed commits in your original repository, the split commits will NOT be signed.*
*This will prevent `monorepo_split.sh` from pushing the unsigned commits to the remote.*  
*To overcome this you can add [the `--force` flag](https://git-scm.com/docs/git-push#git-push--f) to the `git push` calls in the script, but it may cause unforeseen consequences if you're not sure what you're doing.*

### Add a new package into the monorepo

When you have the monorepo, you may find a reason for adding a new package after some time you already use the monorepo.
In this case, don't use `monorepo_build.sh`, but do following steps:

* Create a new repository, for example, *vendor/gamma*
* Add remote into the monorepo `git remote add package-gamma http://github.com/vendor/gamma.git`
* Create a new directory in the monorepo **packages/gamma**
* Add the code and commit it
* Use split tool with the new package
    ```
    ~/monorepo-tools/monorepo_split.sh \
        main-repository package-alpha:packages/alpha package-beta:packages/beta package-gamma:packages/gamma
    ```

## Reference

This is just a short description and usage of all the tools in the package.
For detailed information go to the scripts themselves and read the comments.

### [monorepo_build.sh](./monorepo_build.sh)

Build monorepo from specified remotes. The remotes must be already added to your repository and fetched.

Usage: `monorepo_build.sh <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...`

### [monorepo_split.sh](./monorepo_split.sh)

Split monorepo built by `monorepo_build.sh` and push all `master` branches along with all tags into specified remotes.

Usage: `monorepo_split.sh <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...`

### [monorepo_add.sh](./monorepo_add.sh)

Add repositories to an existing monorepo from specified remotes. The remotes must be already added to your repository and fetched. Only master branch will be added from each repo.

Usage: `monorepo_add.sh <remote-name>[:<subdirectory>] <remote-name>[:<subdirectory>] ...`

### [rewrite_history_into.sh](./rewrite_history_into.sh)

Rewrite git history (even tags) so that all filepaths are in a specific subdirectory.

Usage: `rewrite_history_into.sh <subdirectory> [<rev-list-args>]`

### [rewrite_history_from.sh](./rewrite_history_from.sh)

Rewrite git history (even tags) so that only commits that made changes in a subdirectory are kept and rewrite all filepaths as if it was root.

Usage: `rewrite_history_from.sh <subdirectory> [<rev-list-args>]`

### [original_refs_restore.sh](./original_refs_restore.sh)

Restore original git history after rewrite.

Usage: `original_refs_restore.sh`

### [original_refs_wipe.sh](./original_refs_wipe.sh)

Wipe original git history after rewrite.

Usage: `original_refs_wipe.sh`

### [load_branches_from_remote.sh](./load_branches_from_remote.sh)

Delete all local branches and create all non-remote-tracking branches of a specified remote.

Usage: `load_branches_from_remote.sh <remote-name>`

### [tag_refs_backup.sh](./tag_refs_backup.sh)

Backup tag refs into `refs/original-tags/`

Usage: `tag_refs_backup.sh`

### [tag_refs_move_to_original.sh](./tag_refs_move_to_original.sh)

Move tag refs from `refs/original-tags/` into `refs/original/`

Usage: `tag_refs_move_to_original.sh`

## Contributing
Thank you for your contributions to Shopsys Monorepo Tools package.
Together we are making Shopsys Framework better.

This repository is READ-ONLY.
If you want to [report issues](https://github.com/shopsys/shopsys/issues/new) and/or send [pull requests](https://github.com/shopsys/shopsys/compare),
please use the main [Shopsys repository](https://github.com/shopsys/shopsys).

Please, check our [Contribution Guide](https://github.com/shopsys/shopsys/blob/master/CONTRIBUTING.md) before contributing.

## Support
What to do when you are in troubles or need some help?
The best way is to join our [Slack](https://join.slack.com/t/shopsysframework/shared_invite/zt-11wx9au4g-e5pXei73UJydHRQ7nVApAQ).

If you want to [report issues](https://github.com/shopsys/shopsys/issues/new), please use the main [Shopsys repository](https://github.com/shopsys/shopsys).
