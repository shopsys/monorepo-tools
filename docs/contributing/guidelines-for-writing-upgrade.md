# Guidelines for writing UPGRADE.md

Keep in mind that upgrade instructions are written for users that do not understand our system well, so more clear they are, more useful they are.

* Our users work in a clone of project-base and even when they do the upgrade, their project-base is not upgraded.
  Every time you change/add anything in project-base, write upgrade instruction how to repeat this work
    * for anything with docker, phing, frontend, config, ...
* Make instructions as easy to follow as possible
    * Good example: [postgres upgrade](https://github.com/shopsys/shopsys/blob/master/UPGRADE.md#postgresql-upgrade)
    * Copyable commands are great
    * Bad example: *"Apply changes done in PR..."*
* If you mention a file, make a link for it
    * This is especially important for files in project-base, as users don't have new changes in their project-base
* Link files in an accurate version, because the project evolves in time
    * Good example: [installation using docker - version alpha5](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md)
    * Bad example: [installation using docker - master](https://github.com/shopsys/shopsys/blob/master/docs/installation/installation-using-docker-application-setup.md)
* Write instructions
    * Good example: *"Do this, then that"*
    * Bad example: *"This was done, this was changed"*
