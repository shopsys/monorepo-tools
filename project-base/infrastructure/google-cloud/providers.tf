provider "google" {
  credentials = "${file("service-account.json")}"
  project     = "${var.GOOGLE_CLOUD_PROJECT_ID}"
  region      = "${var.GOOGLE_CLOUD_REGION}"
}

provider "kubernetes" {
  host = "https://${google_container_cluster.primary.endpoint}"
}
