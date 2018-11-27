# How to deploy SSFW to Google Cloud Platform
Before reading this, make sure you read [Introduction to Kubernetes](./introduction-to-kubernetes.md) and [Set up Google Cloud](./set-up-google-cloud.md).

Shopsys Framework provides a way how to deploy your site to Google Cloud using Kubernetes configurations and scripts, Terraform configurations and Kustomize written into repository.

We created shell scripts that encapsulate functionality of Terraform and Kustomize on Google Cloud. This document describes functionality of each technology and their commands.

If you want to know something about the script usage, read [Deploy your application to Google Cloud on your CI/CD](./deploy-your-application-to-google-cloud-on-your-ci-cd.md).

## Intro to Terraform
To be able to create infrastructure on Google Cloud, we need a tool that is able to communicate with Google Cloud API. For this purpose, we have decided to use Terraform. [Terraform](https://www.terraform.io/) is a tool that allows you to create, change and destroy infrastructure on popular cloud providers (like Google Cloud, AWS, etc.) and many [other providers](https://www.terraform.io/docs/providers/).

We use it to declare database providers, clusters and networks on Google Cloud using declarative configurations that are part of the repository. Terraform applies infrastructure based on configuration provided, Shopsys Framework contains prepared configuration in [infrastructure/google-cloud](/project-base/infrastructure/google-cloud).

### Usage Examples

#### Terraform Initialization
Initialization Terraform will get Terraform connected with providers, for example, one of these providers is Kubernetes, Terraform is able to connect with Kubernetes cluster and persist its configuration file which serves as a authentication file.

Command for initialization would look like this:
```bash
terraform init
```

#### Apply infrastructure
Each change of application infrastructure needs to be applied. Terraform creates a `tfstate` file which describes the current state of installed infrastructure, that means that if you change something in infrastructure, Terraform will not apply all changes again, but it will just compare current state with desired state and perform only desired changes.

For applying a change of infrastructure we need to execute this command:

```bash
terraform apply
```

#### Destroy infrastructure
To stop running infrastructure:

```bash
terraform destroy
```

Always keep in mind to have `tfstate` file available, if you lose this file and try to stop running infrastructure, nothing will happen because terraform will not know what resource to stop.

You can read more about Terraform in [official documentation](https://www.terraform.io/docs/index.html)

## Intro to Kustomize
The production environment is a little bit different than the one used on CI. For example, on Google Cloud we use storage tools like [Postgres](https://www.postgresql.org/) and [Redis](https://redis.io/) provided by Google Cloud platform.  

That means that we do not use always the same manifests, with Kustomize you can divide your manifests into `variants`, for example `CI`, `production` etc. These variants are located in [kubernetes/kustomize/overlays](/project-base/kubernetes/kustomize/overlays). Each variant has `kustomization.yml`, which can independently select own manifests using `resources` or generate config maps, create secrets etc.  

### Usage Examples
Select the environment, in our case `production` and go to the variant folder:

```bash
cd kubernetes/overlays/production
```

You can build final manifest from variant by executing:

```bash
kustomize build
```

This outputs a final yaml file into a stream, you can use this output with kubectl to apply it into cluster like this:

```bash
kustomize build | kubectl apply -f -
```

You can read more about Kustomize in their [Github documentation](https://github.com/kubernetes-sigs/kustomize/tree/master/docs)

## Further reading
- Do you want to deploy your application to Google Cloud? Follow instructions in [Deploy your application to Google Cloud on your CI/CD](./deploy-your-application-to-google-cloud-on-your-ci-cd.md)
- Do you want to add a new domain into your existing Google Cloud infrastructure? Read [Add new domain into Google Cloud infrastructure](./domains-in-google-cloud-infrastructure.md)
