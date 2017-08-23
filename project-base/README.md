# Shopsys Framework

## Documentation
For documentation of Shopsys Framework itself see [Shopsys Framework Knowledge Base](docs/index.md).

Documentation of the specific project built on Shopsys Framework should be in [Project Documentation](docs/project/index.md).

## Installation
```
git clone https://git.shopsys-framework.com/shopsys/shopsys-framework.git
cd shopsys-framework
composer install
cp app/config/domains_urls.yml.dist app/config/domains_urls.yml
php phing db-create
php phing test-db-create
php phing build-demo-dev
php phing img-demo
php bin/console server:run
```

When in doubt consult detailed [Installation Guide](docs/introduction/installation-guide.md).

### What to do next
Open [http://127.0.0.1:8000/](http://127.0.0.1:8000/) to see running application.

You can also login into the administration section on [http://127.0.0.1:8000/admin/](http://127.0.0.1:8000/admin/) with default credentials:
* Username: `admin` or `superadmin` (the latter has access to advanced options)
* Password: `admin123`

## Contributing
You can take part in making Shopsys Framework better. See [Contributing guide](CONTRIBUTING.md) for details. 
