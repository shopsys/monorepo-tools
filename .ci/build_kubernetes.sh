#!/bin/sh -ex

# For details about this script, see /docs/kubernetes/continuous-integration-using-kubernetes.md

# Login to Docker Hub for pushing images into register
echo ${DOCKER_PASSWORD} | docker login --username ${DOCKER_USERNAME} --password-stdin

# Set domain names for 2 domains by git branch name and server domain
FIRST_DOMAIN_HOSTNAME=${JOB_NAME}.${DEVELOPMENT_SERVER_DOMAIN}
SECOND_DOMAIN_HOSTNAME=2.${JOB_NAME}.${DEVELOPMENT_SERVER_DOMAIN}

# Set domain name into ingress controller so ingress can listen on domain name
yq write --inplace project-base/kubernetes/ingress.yml spec.rules[0].host ${FIRST_DOMAIN_HOSTNAME}
yq write --inplace project-base/kubernetes/ingress.yml spec.rules[1].host ${SECOND_DOMAIN_HOSTNAME}

# Set domain into dev-stack (redis-admin, elasticsearch, adminer) hostnames
yq write --inplace project-base/kubernetes/kustomize/overlays/ci/ingress-patch.yaml [0].value.host adminer.${FIRST_DOMAIN_HOSTNAME}
yq write --inplace project-base/kubernetes/kustomize/overlays/ci/ingress-patch.yaml [1].value.host elasticsearch.${FIRST_DOMAIN_HOSTNAME}
yq write --inplace project-base/kubernetes/kustomize/overlays/ci/ingress-patch.yaml [2].value.host redis-admin.${FIRST_DOMAIN_HOSTNAME}

# Set domain into webserver hostnames
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.hostAliases[0].hostnames[+] ${FIRST_DOMAIN_HOSTNAME}
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.hostAliases[0].hostnames[+] ${SECOND_DOMAIN_HOSTNAME}

# Set parameters.yml file and domains_urls
cp project-base/app/config/domains_urls.yml.dist project-base/app/config/domains_urls.yml
cp project-base/app/config/parameters_test.yml.dist project-base/app/config/parameters_test.yml
cp project-base/app/config/parameters.yml.dist project-base/app/config/parameters.yml
yq write --inplace project-base/app/config/domains_urls.yml domains_urls[0].url http://${FIRST_DOMAIN_HOSTNAME}:${NGINX_INGRESS_CONTROLLER_HOST_PORT}
yq write --inplace project-base/app/config/domains_urls.yml domains_urls[1].url http://${SECOND_DOMAIN_HOSTNAME}:${NGINX_INGRESS_CONTROLLER_HOST_PORT}

# Change "overwrite_domain_url" parameter for Selenium tests as containers "webserver" and "php-fpm" are bundled together in a pod "webserver-php-fpm"
yq write --inplace project-base/app/config/parameters_test.yml parameters.overwrite_domain_url http://webserver-php-fpm:8080

# Pull or build Docker images for the current commit
DOCKER_IMAGE_TAG=ci-commit-${GIT_COMMIT}
DOCKER_ELASTIC_IMAGE_TAG=ci-elasticsearch

## Docker image for application php-fpm container
docker image pull ${DOCKER_USERNAME}/php-fpm:${DOCKER_IMAGE_TAG} || (
    echo "Image not found (see warning above), building it instead..." &&
    docker image build \
        --build-arg project_root=project-base \
        --tag ${DOCKER_USERNAME}/php-fpm:${DOCKER_IMAGE_TAG} \
        --target ci \
        -f project-base/docker/php-fpm/Dockerfile \
        . &&
    docker image push ${DOCKER_USERNAME}/php-fpm:${DOCKER_IMAGE_TAG}
)

## Docker image for application elasticsearch container
docker image pull ${DOCKER_USERNAME}/elasticsearch:${DOCKER_ELASTIC_IMAGE_TAG} || (
    echo "Image not found (see warning above), building it instead..." &&
    docker image build \
        --tag ${DOCKER_USERNAME}/elasticsearch:${DOCKER_ELASTIC_IMAGE_TAG} \
        -f project-base/docker/elasticsearch/Dockerfile \
        . &&
    docker image push ${DOCKER_USERNAME}/elasticsearch:${DOCKER_ELASTIC_IMAGE_TAG}
)

# Replace docker images for php-fpm of application
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.containers[0].image ${DOCKER_USERNAME}/php-fpm:${DOCKER_IMAGE_TAG}
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[0].image ${DOCKER_USERNAME}/php-fpm:${DOCKER_IMAGE_TAG}
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[1].image ${DOCKER_USERNAME}/php-fpm:${DOCKER_IMAGE_TAG}

# Replace docker image for elasticsearch of application
yq write --inplace project-base/kubernetes/deployments/elasticsearch.yml spec.template.spec.containers[0].image ${DOCKER_USERNAME}/elasticsearch:${DOCKER_ELASTIC_IMAGE_TAG}

# Set different path to parameters and domain configmap paths, as default context is that root of the project is project-base, here it is monorepo so we need to check it
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[0].volumeMounts[1].mountPath /var/www/html/project-base/app/config/domains_urls.yml
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[0].volumeMounts[2].mountPath /var/www/html/project-base/app/config/parameters.yml
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[1].volumeMounts[1].mountPath /var/www/html/project-base/app/config/domains_urls.yml
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[1].volumeMounts[2].mountPath /var/www/html/project-base/app/config/parameters.yml
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.containers[0].volumeMounts[1].mountPath /var/www/html/project-base/app/config/domains_urls.yml
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.containers[0].volumeMounts[2].mountPath /var/www/html/project-base/app/config/parameters.yml

# Set environment variables to container and initContainer for Google Cloud Storage connection - set it to null
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.containers[0].env[0].value ''
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.containers[0].env[1].value ''
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[1].env[0].value ''
yq write --inplace project-base/kubernetes/deployments/webserver-php-fpm.yml spec.template.spec.initContainers[1].env[1].value ''

# Deploy application using kubectl
kubectl delete namespace ${JOB_NAME} || true
kubectl create namespace ${JOB_NAME}
cd project-base/kubernetes/kustomize

# Echo Kustomize build for debugging
kustomize build overlays/ci

# Apply kubernetes manifests by output of Kustomize Build
kustomize build overlays/ci | kubectl apply -f - --namespace=${JOB_NAME}

# Wait for containers to rollout
kubectl rollout status --namespace=${JOB_NAME} deployment/adminer --watch
kubectl rollout status --namespace=${JOB_NAME} deployment/elasticsearch --watch
kubectl rollout status --namespace=${JOB_NAME} deployment/postgres --watch
kubectl rollout status --namespace=${JOB_NAME} deployment/redis --watch
kubectl rollout status --namespace=${JOB_NAME} deployment/redis-admin --watch
kubectl rollout status --namespace=${JOB_NAME} deployment/selenium-server --watch
kubectl rollout status --namespace=${JOB_NAME} deployment/smtp-server --watch
kubectl rollout status --namespace=${JOB_NAME} deployment/webserver-php-fpm --watch

# Find running php-fpm container
PHP_FPM_POD=$(kubectl get pods --namespace=${JOB_NAME} -l app=webserver-php-fpm -o=jsonpath='{.items[0].metadata.name}')

# Run phing build targets for build of the application
kubectl exec ${PHP_FPM_POD} --namespace=${JOB_NAME} ./phing test-db-create test-dirs-create checks-ci
