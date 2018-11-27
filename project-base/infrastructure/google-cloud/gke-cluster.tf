data "google_container_engine_versions" "primary" {
  zone = "${var.GOOGLE_CLOUD_REGION}-a"
}

resource "google_container_cluster" "primary" {
  name               = "primary"
  zone               = "${data.google_container_engine_versions.primary.zone}"
  min_master_version = "${data.google_container_engine_versions.primary.latest_master_version}"
  node_version       = "${data.google_container_engine_versions.primary.latest_node_version}"
  initial_node_count = 3

  node_config {
    oauth_scopes = [
      "https://www.googleapis.com/auth/compute",
      "https://www.googleapis.com/auth/devstorage.read_only",
      "https://www.googleapis.com/auth/logging.write",
      "https://www.googleapis.com/auth/monitoring",
    ]

    machine_type = "n1-standard-2"
  }

  addons_config {
    http_load_balancing {
      disabled = true
    }

    horizontal_pod_autoscaling {
      disabled = true
    }
  }

  provisioner "local-exec" {
    command     = "gcloud container clusters get-credentials ${self.name} --zone ${self.zone} && kubectl create clusterrolebinding cluster-admin-binding --clusterrole cluster-admin --user $(gcloud config get-value account)"
    interpreter = ["/bin/bash", "-c"]
  }
}

output "google-cluster-primary-name" {
  value = "${google_container_cluster.primary.name}"
}

output "google-cluster-primary-zone" {
  value = "${google_container_cluster.primary.zone}"
}
