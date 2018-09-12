# Introduction to Kubernetes

[Kubernetes](https://kubernetes.io) is a production-grade orchestration tool for containerized applications.
It has advanced features for automated deployment, scaling, and management of Docker containers.
The system allows you to describe the infrastructure of your application in a few YAML files, and Kubernetes will build it by running and connecting Docker containers.
After you change your configuration, Kubernetes will again deploy all that is needed, even on several machines.

Kubernetes architecture consists of the server part (a *cluster*), the client (*kubectl*) and the configuration (*manifests*).

For local development, [we use Docker Compose](/docs/introduction/shopsys-framework-on-docker.md) to run and interconnect our containers.
But on our Continuous Integration server, we quickly encountered issues as our requirements increased.
We had to come up with several workarounds, such as [programmatically allocating available ports](/docs/cookbook/jenkins-configuration.md#handling-ports), to enable running several builds in parallel.
This basically meant we ended up writing a simplistic orchestration ourselves...

Furthermore, for deployment of a big scalable application to a production environment, Docker Compose wouldn't suffice.
Demands on the production deployment are much bigger and either native installation with custom deployment process or a full-featured orchestration can be used.

Choosing orchestration over native installation gives us the possibility of having infrastructure as code, and makes the developer and production environment more similar.
This, of course, lowers the amount of production-only issues and helps with debugging.

We don't recommend using Kubernetes for local development, Docker Compose is a more suitable tool.

## Why choose Kubernetes over other orchestration tools?
We have looked into other tools, such as Docker Swarm, Mesos or Nomad.
Kubernetes has all features we determined as critical for production:
- service discovery
- secret management
- logging and monitoring
- load balancing
- auto-scaling
- self-healing

Even though the technology is quite complicated to understand and set up, its features, wide-spread adoption, and active development made it the best choice.

A nice advantage is that it's configured via YAML files, which we use for most of our configuration.

## Terminology of Kubernetes
Kubernetes uses its own terminology and it can be difficult to get into.
Here is a summary of the basic term used in Kubernetes:
- **K8s** is an acronym for Kubernetes (similar to *i18n* for *internationalization*).
We try to avoid using it for clarity.
- **Kubernetes Cluster** is an abstraction over a set of servers (*nodes*).
You can deploy your application directly into a *cluster*.
For practical details, see [How to Get a Cluster Running](/docs/kubernetes/how-to-get-a-cluster-running.md).
- **Node** is a part of a *cluster*. It is an abstraction over a single server.
- **kubectl** is a CLI client for remotely controlling your *cluster*.
You can run `kubectl` to deploy your application, access logs or connect directly into a running container.
It's similar to running `docker` or `docker-compose` to control your local containers.
- **Pod** is a set of containers that always run on a single *node*.
All containers in Kubernetes run a *pod* (you can have a *pod* with a single container).  
A *pod* always has a default container, so if you execute something in a *pod* without specifying a container, you'll execute it in the default one.
- **Deployment** is a declarative configuration (manifest) of *N* copies of a *pod*.
Basically a recipe for running a *pod*, with the possibility of scaling it.
- **Service** is a way to configure a *deployment* to receive traffic.
Without a *service* configured, a *deployment* (or a *pod*) cannot be accessed, even from within the same *cluster*.
- **Ingress** is an application that manages external HTTP access to your configured *services*.
You could liken it to a web-server.
- **Namespace** is a way to group and separate different networks.
This allows you to easily deploy several containerized applications to a single *cluster*.
The *namespace* can be provided for every command via `kubectl [command] --namespace=my-app` or you can switch your *namespace* globally.

For more info, see [official Kubernetes documentation](https://kubernetes.io/docs/home/?path=users&persona=app-developer).
