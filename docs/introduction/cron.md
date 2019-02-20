# Cron

## Basics
Cron is a tool to run background jobs and is essential for the production environment.
Periodically executed Cron modules recalculate visibility, generate XML feeds and sitemaps, provide error reporting etc.

## Default Cron Commands
There is some prepared configuration for Shopsys Framework in a file `src/Resources/config/services/cron.yml` in `FrameworkBundle`.

## Running Cron Jobs
Do not forget to set up a cron on your server to execute [`php phing cron`](/docs/introduction/console-commands-for-application-management-phing-targets.md#cron) every 5 minutes.

## Multiple Cron Instances
By default, all cron jobs are run as part of one, default, instance.
However, you may want to have several instances to be able to run, for example, lots of transfers from/into ERP systems and these transfers could block other cron processes.
Separating the cron jobs into two (or more) cron instances allows you to run some jobs in parallel.

The instance of cron is actually a named group of cron jobs.

You can learn how to set up multiple cron instances in [Working with Multiple Cron Instances](/docs/cookbook/working-with-multiple-cron-instances.md) cookbook.
