# Shopsys Framework
Shopsys Framework is a **scalable e-commerce framework** for fast-growing e-commerce sites created and maintained by in-house developers or outsourcing agencies.

Our product provides the tools and know-how **to help save thousands of developer man-hours** in the short and long-term growth of e-merchants and their websites. 

A typical project using our framework is a **B2B or B2C site with a yearly revenue ranging from €5M to €100M,
thousands of orders and hundreds of thousands of pageviews each day**.

## Shopsys Framework Infrastructure
![Shopsys Framework Infrastructure](./docs/img/shopsys-framework-infrastructure.png 'Shopsys Framework Infrastructure')

## Shopsys Framework Package Architecture
These are most important packages and the way they depend on each other.
For more info see the article [Basics About Package Architecture](./docs/introduction/basics-about-package-architecture.md) in our knowledge base.

![Shopsys Framework package architecture schema](./docs/img/package-architecture.png 'Shopsys Framework Package Architecture')

## Current State and a Roadmap

### State in March 2018
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

Last stable release of Shopsys 6.1 was internally published at the beginning of 2018 and on this version we created several big B2C and B2B projects.
Experience we got through implementations lead us to ideas and plans for next version of our Shopsys Framework.
The main change is a bigger focus on performance and scalability and significant architecture changes which will provide easy upgradability.
You can read a full article about our goals
[on our blog](https://blog.shopsys.com/shopsys-framework-goals-for-the-beta-and-the-stabile-version-9facf4763376).

Shopsys Framework is currently in the process of architecture refactoring.
Because of this fact, there will be lots of BC breaks in next few months and architecture is not consistent at the moment.
**So we strictly recommend to use the last stable version of Shopsys 6.1 for production - contact us and we will provide you the access for free.**

### Summer 2018 - Alpha
* Experimental projects to validate upgradability
* Heavy performance testing
* Security audits

### September 2018 - Open beta
* Performance optimization through Elasticsearch, Redis, PostgreSQL
* Full core upgradability
* GDPR compliance
* First modules

### February 2019 - Stable version
* Ready to scale
* Asynchronous Processing (RabbitMQ)
* API for front-end applications
* Module store (10 modules)
* Best practice manuals

You can learn more about our development plans [on our blog](https://blog.shopsys.com/here-it-is-shopsys-framework-development-roadmap-154edb549c97). 

## Sites Built on Shopsys Framework
List of typical projects built on previous versions of Shopsys Framework:
* [Zoopy](https://www.zoopy.cz/)
* [Prumex](https://www.prumex.cz/)
* [Elektro Vlášek](https://www.elektrovlasek.cz/)
* [AB COM CZECH](https://www.ab-com.cz/)
* [Knihy.cz](https://www.knihy.cz/)

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

* [Community License](./LICENSE) in MIT style for small to mid-size e-commerce sites with yearly total online sales less than €10.000.000
* Commercial License

Learn the principles on which we distribute our product on our website at [Licenses and Pricing section](https://www.shopsys.com/licensing).

The rest of modules of Shopsys Framework including [HTTP smoke testing](https://github.com/shopsys/http-smoke-testing) are distributed under standard MIT license. 

