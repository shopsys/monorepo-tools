terraform {
  backend "local" {
    path = "tfstate/terraform.tfstate"
  }
}