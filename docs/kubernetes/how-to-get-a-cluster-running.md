# How to Get a Cluster Running
Installation and maintenance of cluster server is complicated and requires a dev-ops maintainer.

We highly recommend you to let your cluster installation to your server provider and consult this with professionals.

## Minimal Setup for Testing
If you just want to try it out and you decided to install a cluster on your own you can follow these steps.

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

Enable Kubelet as a service so it starts with the system reboot
```bash
systemctl enable kubelet && systemctl start kubelet
```

Kubernetes works with iptables rules for setting up traffic between pods. 
That's why there is a need to turn off some security processes to assure that Kubernetes will work properly.

Disable `setenforce` process that is in conflict with Kubernetes:
```bash
setenforce 0
```

Disable `swap` because Kubernetes works with memory used onto server, which cannot be controlled if swap is turned on:
```bash
swapoff -a
```

Clean already created rules in iptables that can be in conflict with Kubernetes:
```bash
cat <<EOF >  /etc/sysctl.d/k8s.conf
net.bridge.bridge-nf-call-ip6tables = 1
net.bridge.bridge-nf-call-iptables = 1
EOF
sysctl --system
```

Create a cluster on your server and define IP range for pods.
```bash
kubeadm init --pod-network-cidr=192.168.0.0/16
```

Make your server a master node:
```bash
kubectl taint nodes --all node-role.kubernetes.io/master-
```

## Start Ingress nginx controller
To forward traffic into the pods you need to start a service that will be listening on the domain and forward traffic into the pods by domain names or ports.

For this we use [Ingress Nginx Controller](https://kubernetes.github.io/ingress-nginx/) maintained by the Kubernetes community.

Download the [manifest](https://raw.githubusercontent.com/kubernetes/ingress-nginx/master/deploy/mandatory.yaml).
If you don't want to let Ingress listen on the default port 80, you need to add `hostPort` property and set the port of your choice into it.

This port is then set into environment variable `$NGINX_INGRESS_CONTROLLER_HOST_PORT`.
