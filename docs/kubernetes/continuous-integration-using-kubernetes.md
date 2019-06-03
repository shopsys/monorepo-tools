# Continuous Integration Using Kubernetes
While using just Docker Compose for our CI, we ran into problems with port allocation and IP addresses range allowed for Docker.
We ended up with our own solution to orchestration which was not perfect and it was hard to replicate onto another server CI.

More about this solution can be found in the [Jenkins Configuration cookbook](/docs/cookbook/jenkins-configuration.md).

With this in mind, we decided to let go of our orchestration solution and let Kubernetes solve our problems.

Introduction to Kubernetes and why we decided to use it can be found in [Introduction to Kubernetes](/docs/kubernetes/introduction-to-kubernetes.md).

## How it works
Our CI builds every branch pushed into the repository.
It performs the load of data fixtures, initialization of databases, tests, checks and deploying of the prepared application onto a node where it can be seen under a specific domain name.

### Choose your CI
Selection of the CI is important.
We currently have really great experience with Jenkins CI.
Mainly because of its freedom with jobs, where it gives you opportunity to write any shell code you want.
That means that you can control things like workspaces clean up, or have control over Kubernetes namespaces which can be crucial with server resources.

### Prepare your CI
There are couple of prerequisites that need to be installed onto the server running the CI.
Our server is running [CentOS 7](https://www.centos.org/).
Following commands are for CentOS and may be different on other distributions.

Install repositories required by Docker and Kubernetes:
```bash
yum install -y yum-utils device-mapper-persistent-data lvm2
```

Install Docker:
```bash
yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

yum install -y docker-ce

systemctl enable docker && systemctl start docker
```

Install Kubernetes and tools for controlling it (Kubelet, Kubectl, Kubeadm):
```bash
yum install -y kubelet kubeadm kubectl --disableexcludes=kubernetes
```

Enable Kubelet as a service so it starts with system reboot:
```bash
systemctl enable kubelet && systemctl start kubelet
```

##### Get a Cluster Kubernetes Config
Application needs to run somewhere, that's where cluster comes in play.
Kubernetes can perform Kubernetes operations on a node using single configuration file.

This configuration file can be found on the node server in `/etc/kubernetes/admin.conf`.

This configuration needs to be copied into `~/.kube/config` on your CI server.
The config must be saved into the home directory of the user running the builds.
For example, on Jenkins CI the builds are ran as a user `jenkins` so the configuration file is saved like this: `/var/lib/jenkins/.kube/config`.

For more about the cluster configuration, see [How to Get a Cluster Running](/docs/kubernetes/how-to-get-a-cluster-running.md).

#### Kustomize
In `.ci/build_kubernetes.sh` we use Kustomize to generate Kubernetes manifests for our environment, which is CI. Kustomize can distinguish manifests used for each environment by selecting resources used by each environment.

You can read more about what Kustomize does [here](./how-to-deploy-ssfw-to-google-cloud-platform.md#kustomize), or you can read their [official documenation](https://github.com/kubernetes-sigs/kustomize/tree/master/docs)

#### Provide Environment Variables
Build cannot be performed without providing some environment variables.
During build process, CI performs operations such as pushing Docker images into Docker Hub or setting the domain names which are different for each project.

You must provide the following environment variables:

| Environment variable                    | Explanation
| --------------------                    | -----------
| **$WORKSPACE**                          | directory where the repository is cloned (used only for mounting the volume)
| **$DEVELOPMENT_SERVER_DOMAIN**          | domain name of your server where your application will be running
| **$DOCKER_USERNAME**                    | your user name on Docker Hub
| **$DOCKER_PASSWORD**                    | your password to Docker Hub
| **$GIT_COMMIT**                         | hash of the built commit, used for tagging images
| **$JOB_NAME**                           | branch name used for domain names and build process
| **$NGINX_INGRESS_CONTROLLER_HOST_PORT** | port used in the ingress controller running on node, don't use 80 (for details, see [How to Get a Cluster Running](/docs/kubernetes/how-to-get-a-cluster-running.md))

### Build
Build of the application is then executed by one single command since the build process is saved into shell file which is part of the repository.

Build is performed using a container running [shopsys/kubernetes-buildpack](https://github.com/shopsys/kubernetes-buildpack) that is prepared for our build process.

Copy this command into your CI build configuration:
```bash
docker run \
    -v $WORKSPACE:/tmp \
    -v ~/.kube/config:/root/.kube/config \
    -v /var/run/docker.sock:/var/run/docker.sock \
    -e DEVELOPMENT_SERVER_DOMAIN \
    -e DOCKER_USERNAME \
    -e DOCKER_PASSWORD \
    -e GIT_COMMIT \
    -e JOB_NAME \
    -e NGINX_INGRESS_CONTROLLER_HOST_PORT \
    --rm \
    shopsys/kubernetes-buildpack \
    .ci/build_kubernetes.sh
```

This command executes [`build_kubernetes.sh`](/.ci/build_kubernetes.sh) in the `shopsys/kubernetes-buildpack` container.

To summarize, the shell script prepares the application config files and Kubernetes manifests for deployment, copies the prepared source code into the image and installs Composer dependencies in the application `php-fpm` container.
Then it builds `elasticsearch` image with ICU Analysis plugin and both images are then pushed and tagged into Docker Hub and these tags are then set into Kubernetes manifests.

In the end, Kubernetes applies these manifests onto the node server where pods are created as a running application.

## Limits of CI using Kubernetes
Since our CI is running on the same machine as our cluster, we can get into a situation where there are too many running applications which may overload the server.

The problem is that right now our application is deployed with all services (such as Postgres or Elasticsearch) created independently for each branch.
This approach leaves you with for example 20 running Postgreses on one server which is really performance heavy.

These problems have solutions which can be implemented:

### Share resources among pods
One of these solutions is to run databases natively on the server and connect the pods onto these databases on a single instance of Postgres.

### Multiple nodes
You can run multiple nodes connected to each other and distribute pods among them.

Right now, we do not have experience with any of these solutions and we decided to always delete the whole Kubernetes namespace after the build is finished and start them only if we need to.
