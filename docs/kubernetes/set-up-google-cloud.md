# Set up Google Cloud

Before deploying to Google Cloud you need to set up Google Cloud correctly to be able to control, create and destroy resources using API requests.

This document is a step by step cookbook on how to set up Google Cloud this way.

For these steps you will need to install gcloud command tool, you can install it by yourself by these instructions, or you can use [shopsys/kubernetes-buildpack](https://github.com/shopsys/kubernetes-buildpack/) where is `gcloud` tool already installed.

If you choose to use `kubernetes-buildpack`, start buildpack container like this

```
docker run -it shopsys/kubernetes-buildpack sh
```

## 1. Create a user
Create a user on Google Cloud. Read more [here](https://console.cloud.google.com/getting-started)

## 2. Create a project.
Create a project on Google Cloud. Read more [here](https://cloud.google.com/resource-manager/docs/creating-managing-projects)

## 3. Obtain Google Cloud parameters and service-account.json

1. service-account.json, read more [here](https://cloud.google.com/iam/docs/creating-managing-service-account-keys)

1. Project id

1. Your login name to Google Cloud

Save these variables, they will be used later, now you can configure your Google Cloud account

## Configure Google Cloud account

First of all you need to login and verify your account, execute this and follow instructions outputted to your console:

`project-name` used as a variable is project id obtained before.

```
gcloud auth login
```

Create new Google Cloud project:
```
gcloud projects create --set-as-default --name <project-name>
```

Now we need to link our billing account for google to be able to create services that are paid.

List your billing accounts:
```
gcloud beta billing accounts list
```

Select one and link project to a billing account:
```
gcloud beta billing projects link <project-name> --billing-account <billing-account-id>
```

Create service account and key for Terraform:
```
gcloud iam service-accounts create terraform --display-name "Terraform admin account"
gcloud projects add-iam-policy-binding <project-name> --member serviceAccount:terraform@<project-name>.iam.gserviceaccount.com --role roles/owner
gcloud iam service-accounts keys create service-account.json --iam-account terraform@<project-name>.iam.gserviceaccount.com
```

Create service account for Google Cloud Storage account
```
gcloud iam service-accounts create gcs-service-account --display-name "GCS admin account"
gcloud projects add-iam-policy-binding <project-name> --member serviceAccount:gcs-service-account@<project-name>.iam.gserviceaccount.com --role roles/storage.admin
```

Enable Google Cloud APIs that are used for deploy
```
gcloud services enable cloudbilling.googleapis.com
gcloud services enable cloudresourcemanager.googleapis.com
gcloud services enable iam.googleapis.com
gcloud services enable compute.googleapis.com
gcloud services enable sqladmin.googleapis.com
gcloud services enable container.googleapis.com
gcloud services enable redis.googleapis.com
```

Now your Google Account should be ready for deploy.

Now you can follow to [Deploy Your Application to Google Cloud on Your CI/CD](./deploy-your-application-to-google-cloud-on-your-ci-cd.md)
