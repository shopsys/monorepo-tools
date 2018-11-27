resource "kubernetes_namespace" "ingress-nginx" {
  depends_on = ["google_container_cluster.primary"]

  metadata {
    name = "ingress-nginx"
  }

  provisioner "local-exec" {
    command     = "kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/master/deploy/mandatory.yaml"
    interpreter = ["/bin/bash", "-c"]
  }
}

resource "kubernetes_service" "ingress-nginx" {
  metadata {
    name      = "ingress-nginx"
    namespace = "${kubernetes_namespace.ingress-nginx.id}"
  }

  spec {
    type             = "LoadBalancer"
    load_balancer_ip = "${google_compute_address.loadbalancer-ip.address}"

    port = {
      name        = "http"
      port        = 80
      target_port = 80
    }

    port = {
      name        = "https"
      port        = 443
      target_port = 443
    }

    selector {
      "app.kubernetes.io/name"    = "ingress-nginx"
      "app.kubernetes.io/part-of" = "ingress-nginx"
    }
  }
}
