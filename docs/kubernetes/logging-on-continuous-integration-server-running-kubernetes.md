# Logging on Continuous Integration server running Kubernetes
As this [article](/docs/introduction/logging.md) describes, our logs are streamed. Since we want to be able to look at logs on our CI without needing to perform `kubectl` commands on server we need to make simple workaround in order to get logs out of application and containers onto local storage.

## Problem
As we do not want to have much instances of application running at once on our servers because of heavy load we delete after each build `kubernetes namespace`. Deleting namespace means that all running pods will be deleted with their logs.

## Our way
We decided to go the simplest way possible. In order to get logs for developers to see easily, we print the output of `kubectl logs` into files saved in jenkins workspace.

We perform this operation every time before we delete the namespace. That way we can get logs really easy with minimum effort.

## Acceptance Tests
Logs of Acceptance Tests cannot be streamed because they export images into application folder. In this case we use `kubectl cp` which is able to copy files (/var/log/codeception) from container to local folder.

## Scripts
In [.ci](/.ci) folder you can find [export_logs.sh](/.ci/export_logs.sh) file which we use on our CI to export logs.

