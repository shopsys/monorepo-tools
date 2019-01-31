# How to set SMTP Server Container

For sending e-mails from our application we use [SMTP server container](https://hub.docker.com/r/namshi/smtp/) in separate `k8s POD` so we need to set some additional settings as permission to be able to send e-mails from `webserver-php-fpm` POD.
For this purpose, we have set `RELAY_NETWORKS` ENV variable with all private networks that can be created by docker into `/project-base/kubernetes/deployments/smtp-server.yml` for POD of smtp container ([#777](https://github.com/shopsys/shopsys/pull/777))  
for instance:
```yaml
image: namshi/smtp:latest
env:
-   name: RELAY_NETWORKS
    value: :192.168.0.0/16:10.0.0.0/8:172.16.0.0/12
```
