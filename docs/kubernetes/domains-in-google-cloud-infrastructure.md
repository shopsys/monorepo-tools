# Domains in Google Cloud infrastructure
If you add a new domain, you also want to have this domain available on Google Cloud. This article describes how to perform such changes.

To pass traffic through domain name you need to modify `ingress.yml` and `webserver-php-fpm.yml` which serves as webserver and listens on domain names.

## Adding a new domain

Let's say we want to listen to a new domain name.

Open [ingress.yml](/project-base/kubernetes/ingress.yml) file and add new domain block into `spec -> rules`:

```
// kubernetes/ingress.yml

rules:
    -   host: ~
        http:
            paths:
            -   path: /
                backend:
                    serviceName: webserver-php-fpm
                    servicePort: 8080
```

Create secrets from SSL certificate in `ingress-patch.yaml` in `kubernetes/kustomize/overlays/production/ingress-patch.yaml`:
```
// kubernetes/kustomize/overlays/production/ingress-patch.yaml

- name: domain-${DOMAIN_NUMBER}-ssl-certificates
  commands:
    tls.key: "cat ${ANOTHER_DOMAIN_SSL_DIRECTORY}/tls.key"
    tls.crt: "cat ${ANOTHER_DOMAIN_SSL_DIRECTORY}/tls.crt"
    ca.crt: "cat ${ANOTHER_DOMAIN_SSL_DIRECTORY}/ca.crt"
```

*Note: Replace ${DOMAIN_NUMBER} with a number of a domain*

Next, add SSL certificates for new domain:

```
spec:
    tls:
    -   hosts:
        secretName: domain-${DOMAIN_NUMBER}-ssl-certificates
```

Open [.ci/deploy-to-google-cloud.sh](/project-base/.ci/deploy-to-google-cloud.sh) file and set your new domain host to `ingress.yml` and `webserver-php-fpm.yml` host name:

```
// .ci/deploy-to-google-cloud.sh

NEW_DOMAIN_HOST=${NEW_DOMAIN_HOST}
yq write --inplace kubernetes/ingress.yml spec.rules[${DOMAIN_INDEX}].host ${NEW_DOMAIN_NAME}
yq write --inplace kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.hostAliases[0].hostnames[+] ${NEW_DOMAIN_NAME}
```

Do not forget to pass `NEW_DOMAIN_HOST` and `ANOTHER_DOMAIN_SSL_DIRECTORY` to `shopsys/kubernetes-buildpack` as ENV variable.

Now you need to add volume with your certificates in execution of `deploy-to-google-cloud.sh` script

```bash
docker run \
    -v $WORKSPACE:/tmp \
    -v /var/run/docker.sock:/var/run/docker.sock \
    -v ~/google-cloud/.terraform/tfstate:/tmp/infrastructure/google-cloud/tfstate \
    -v ~/google-cloud/service-account.json:/tmp/infrastructure/google-cloud/service-account.json \
    -v ~/path/to/certificates-1:$FIRST_DOMAIN_SSL_DIRECTORY \
    -v ~/path/to/certificates-2:$SECOND_DOMAIN_SSL_DIRECTORY \
+   -v ~/path/to/certificates-3:$THIRD_DOMAIN_SSL_DIRECTORY
    -e DOCKER_USERNAME \
    -e DOCKER_PASSWORD \
    -e GIT_COMMIT \
    -e FIRST_DOMAIN_HOSTNAME \
    -e SECOND_DOMAIN_HOSTNAME \
    -e PROJECT_ID \
    -e FIRST_DOMAIN_SSL_DIRECTORY \
    -e SECOND_DOMAIN_SSL_DIRECTORY \
    --rm \
    shopsys/kubernetes-buildpack:0.2.0 \
    .ci/deploy-to-google-cloud.sh
```

After executing this, changes will be applied and the new domain is up and running now.