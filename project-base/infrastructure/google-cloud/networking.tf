resource "google_compute_address" "loadbalancer-ip" {
  name   = "loadbalancer-ip"
  region = "${var.GOOGLE_CLOUD_REGION}"
}

output "loadbalancer-ip" {
  value = "${google_compute_address.loadbalancer-ip.address}"
}
