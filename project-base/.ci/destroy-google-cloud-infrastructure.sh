#!/bin/sh -ex

cd /tmp/infrastructure/google-cloud

# Authenticate yourself with service.account.json file.
export GOOGLE_APPLICATION_CREDENTIALS=/tmp/infrastructure/google-cloud/service-account.json
gcloud config set container/use_application_default_credentials true

# Activate service account
gcloud auth activate-service-account --key-file=service-account.json

# Set project by ID into gcloud config
gcloud config set project ${PROJECT_ID}

gcloud config get-value account

# Export terraform variables
export TF_VAR_GOOGLE_CLOUD_ACCOUNT_ID=$(gcloud config get-value account | sed "s#@.*##")
export TF_VAR_GOOGLE_CLOUD_PROJECT_ID=${PROJECT_ID}
export TF_VAR_GOOGLE_CLOUD_STORAGE_BUCKET_NAME=${GOOGLE_CLOUD_STORAGE_BUCKET_NAME}

# Get credentials to the kubernetes cluster for "kubectl" command from gcloud if the cluster is already provisioned
if [ -n "$(terraform output google-cluster-primary-name 2> /dev/null)" ]; then
    gcloud container clusters get-credentials $(terraform output google-cluster-primary-name) --zone $(terraform output google-cluster-primary-zone)
fi

# Destroy infrastructure
terraform destroy --auto-approve