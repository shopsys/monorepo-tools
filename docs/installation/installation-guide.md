# Installation Guide

This document will provide you with information about 2 ways of developing and runninng the Shopsys Framework project and the services that it depends on.  
The first option [using docker](#installation-using-docker) is **highly recommended** since it is the easiest and fastest way to start Shopsys Framework.
In the case the operation system does not support docker, or you are not able to use Docker (e.g. due to the performance problems with Docker-sync), we prepared also second section with document about project [installation without docker](#installation-without-docker), however this way is slower and harder to configure and maintain because of different operation systems and their versions.

## Installation using Docker

These guides will show you how to use prepared Docker Compose configuration to simplify the installation process.
Docker contains complete development environment necessary for running your application so you do not need to install and configure the whole server stack (Nginx, PostgreSQL, etc.) natively in order to run and develop Shopsys Framework on your machine.  
All the services needed by Shopsys Framework like Nginx or PostgreSQL run in Docker and your source code is automatically synchronized between your local machine and Docker container in both ways.  
That means that you can normally use your IDE to edit the code while it is running inside a Docker container.

- [Linux](installation-using-docker-linux.md)
- [MacOS](installation-using-docker-macos.md)
- [Windows 10 Pro and higher](installation-using-docker-windows-10-pro-higher.md)

## Installation without Docker

If your system is not listed above or you do not want to use Docker containers, you can still install it natively.
To develop and run Shopsys Framework natively you can read the [native installation](native-installation.md) document.
This document is not step-by-step guide since support for all operation systems and their versions is very hard to maintain.
