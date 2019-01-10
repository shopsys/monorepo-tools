# Shopsys Framework
Shopsys Framework is a **fully functional ecommerce platform for businesses transitioning into tech-companies with their own software development team**. 
It contains the most common B2C and B2B features for online stores, and its infrastructure is prepared for high scalability.

Shopsys Framework is **the fruit of our 15 years of experience in creating custom-made online stores and it’s dedicated to best in-house devs teams who work with online stores with tens of millions of Euros of turnover per year**. 

Our platform’s **architecture is modern and corresponds to the latest trends in the production of software for leading ecommerce solutions**. 
Deployment and scaling of our system are comfortable thanks to the use of the containerization and orchestration concepts (**Docker, Kubernetes**). 
The platform is based on one of the best PHP frameworks on the market - **Symfony**.

## Shopsys Framework Infrastructure
![Shopsys Framework Infrastructure](./docs/img/shopsys-framework-infrastructure.png 'Shopsys Framework Infrastructure')

## Shopsys Framework Package Architecture
These are most important packages and the way they depend on each other.
For more info see the article [Basics About Package Architecture](./docs/introduction/basics-about-package-architecture.md) in our knowledge base.

![Shopsys Framework package architecture schema](./docs/img/package-architecture.png 'Shopsys Framework Package Architecture')
*Note: The specific modules in this diagram are just examples.*

## Current State and Roadmap

### Current State

Shopsys Framework is fully functional e-commerce platform with all basic functionality all e-commerce sites needs:
* product catalogue
* registered customers
* basic orders management
* back-end administration
* front-end full-text search and product filtering
* 3-step ordering process
* basic CMS
* support for several currencies, languages, and domains
* full friendly URL for main entities
* Performance optimization through Elasticsearch, Redis, PostgreSQL
* Full core upgradability
* GDPR compliance
* Preparation for scalability
* Manifest for orchestration via Kubernetes

### Further Plan for Stable Release (February 2019)

* More performance optimizations
* Modulestore with first modules
* Asynchronous Processing (RabbitMQ)
* Best practice manuals
* Basic API

## Sites Built on Shopsys Framework
List of typical projects built on previous versions of Shopsys Framework:
* [Zoopy](https://www.zoopy.cz/)
* [Prumex](https://www.prumex.cz/)
* [Elektro Vlášek](https://www.elektrovlasek.cz/)
* [AB COM CZECH](https://www.ab-com.cz/)
* [Knihy.cz](https://www.knihy.cz/)
* [B2B portal Démos](https://beta.demos24plus.com/login/)
* [Agátin svět](https://www.agatinsvet.cz/)

## How to Start a New Project
The *shopsys/shopsys* package is a monolithic repository, a single development environment, for management of all parts of Shopsys Framework.
See more information about the monorepo approach in [the Monorepo article](./docs/introduction/monorepo.md).

For the purposes of building a new project use our [shopsys/project-base](https://github.com/shopsys/project-base),
which is fully ready as the base for building your Shopsys Framework project.

We recommend to choose **installation via Docker** because it is the easiest and fastest way to start using Shopsys Framework.
Docker contains complete development environment necessary for running your application.
In the future we want to add new technologies to Shopsys Framework (e.g. ElasticSearch).
**Updating your development environment to use these technologies will be very easy with Docker**
because such an update will be done just by running `docker-compose build`.
And that is all!

For more detailed instructions, follow one of the installation guides:

* [Installation via Docker (recommended)](docs/installation/installation-using-docker.md)
* [Native installation](docs/installation/native-installation.md)

## Documentation
For documentation of Shopsys Framework itself, see [Shopsys Framework Knowledge Base](./docs/index.md).

For the frequently asked questions, see [FAQ and Common Issues](./docs/introduction/faq-and-common-issues.md).

## Contributing
If you have some ideas or you want to help to improve Shopsys Framework, let us know!
We are looking forward to your insights, feedback, and improvements.
Thank you for helping us making Shopsys Framework better.

You can find all the necessary information in our [Contribution Guide](./CONTRIBUTING.md). 

## Support
What to do when you are in troubles or need some help?
The best way is to contact us on our [Slack](http://slack.shopsys-framework.com/).

If you are experiencing problems during installation or running Shopsys Framework on Docker,
please see our [Docker troubleshooting](./docs/docker/docker-troubleshooting.md).

Or ultimately, just [report an issue](https://github.com/shopsys/shopsys/issues/new).

## License
We distribute our main parts of Shopsys Framework
[shopsys/project-base](https://github.com/shopsys/project-base) and
[shopsys/framework](https://github.com/shopsys/framework) under two different licenses: 

* [Community License](./LICENSE) in MIT style for growing small to mid-size e-commerce sites with total online sales less than 12.000.000 EUR / year (3.000.000 EUR / quarter)
* Commercial License

Learn the principles on which we distribute our product on our website at [Licenses and Pricing section](https://www.shopsys.com/licensing).

The rest of modules of Shopsys Framework including [HTTP smoke testing](https://github.com/shopsys/http-smoke-testing) are distributed under standard MIT license. 

Shopsys Framework also uses some third-party components and images which are licensed under their own respective licenses.
The list of these licenses is summarized in [Open Source License Acknowledgements and Third Party Copyrights](./open-source-license-acknowledgements-and-third-party-copyrights.md).
