# License Overview of Included 3rd Party Libraries

Shopsys Framework is licensed under the terms of the [Shopsys Community License](./LICENSE).

Shopsys Framework also uses some third-party components and images
which are licensed under their own respective licenses.

## Main components used by Shopsys Framework
These components are installed via `composer` or via `npm`.
You can check all the dependencies using the instructions from the section Libraries dynamically referenced via Composer and Libraries dynamically referenced via npm.

### Symfony Framework and Symfony Components
License: MIT  
https://symfony.com/doc/3.4/contributing/code/license.html

### Elasticsearch
License: Apache License 2.0  
https://github.com/elastic/elasticsearch-php/blob/master/LICENSE

### Grunt: The JavaScript Task Runner
License: MIT  
https://github.com/gruntjs/grunt/blob/master/README.md

### Phing
License: LGPL-3.0-only  
https://github.com/phingofficial/phing/blob/master/LICENSE

### Nette Foundation tools 
License: BSD-2-Clause or GPL-2.0 or GPL-3.0  
https://nette.org/en/license

### PHP_CodeSniffer
License: BSD-3-Clause  
https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt

### PHP Parallel Lint
License: BSD-2-Clause  
https://github.com/JakubOnderka/PHP-Parallel-Lint/blob/master/LICENSE

### ESLint
License: MIT  
https://github.com/eslint/eslint/blob/master/LICENSE

### jQuery
License: MIT or GPL Version 2  
http://jquery.org/license

### selectize.js
License: Apache License 2.0  
https://github.com/selectize/selectize.js/blob/master/LICENSE

### slick.js
License: MIT  
https://github.com/kenwheeler/slick/blob/master/LICENSE

## Images and libraries installed for a full run of Shopsys Framework on Docker
These images and packages are configured in `docker-compose.yml` and in `Dockerfile`.

### Postgres
Image: `Postgres:10.5-alpine`  
License: PostgreSQL License  
https://www.postgresql.org/about/licence/

### Nginx
Image: `Nginx:1.13-alpine`  
License: BSD-2-Clause  
http://nginx.org/LICENSE

### Redis
Image: `Redis:4.0-alpine`  
License: BSD-3-Clause  
https://redis.io/topics/license

### phpRedisAdmin
Image: `Erikdubbelboer/phpredisadmin:v1.10.2`  
License: Creative Commons Attribution 3.0 BY  
https://github.com/erikdubbelboer/phpRedisAdmin/blob/master/README.markdown

### Selenium Docker
Image: `Selenium/standalone-chrome:3.11`  
License: Apache License 2.0  
https://github.com/SeleniumHQ/docker-selenium/blob/master/LICENSE.md

### Adminer
Image: `Adminer:4.6`  
License: Apache License 2.0 or GPL 2  
https://github.com/vrana/adminer/blob/master/readme.txt

### Elasticsearch
Image: `Docker.elastic.co/elasticsearch/elasticsearch-oss`  
License: Apache License 2.0  
https://github.com/elastic/elasticsearch/blob/66b5ed86f7adede8102cd4d979b9f4924e5bd837/LICENSE.txt

### Php
Image: `php:7.2-fpm-alpine`  
License: The PHP License  
http://php.net/license/

### GNU libiconv
Package: `gnu-libiconv`  
License: LGPL  
https://pkgs.alpinelinux.org/package/edge/testing/x86/gnu-libiconv

### Composer - Dependency Management for PHP
License: MIT  
https://github.com/composer/composer/blob/master/LICENSE

### grunt-cli
License: MIT  
https://github.com/gruntjs/grunt-cli/blob/master/LICENSE-MIT

### nodejs-npm
License: Artistic License 2.0  
https://www.npmjs.com/policies/npm-license

### prestissimo (composer plugin)
License: MIT  
https://github.com/hirak/prestissimo/blob/master/LICENSE

### libpng-dev
License: GPL  
https://pkgs.alpinelinux.org/package/v3.3/main/x86/libpng-dev

### icu-dev
License: MIT or ICU or Unicode-TOU    
https://pkgs.alpinelinux.org/package/edge/main/x86/icu-dev

### postgresql-dev
License: PostgreSQL  
https://pkgs.alpinelinux.org/package/edge/main/x86/postgresql-dev

### libzip-dev
License: BSD-3-clause  
https://pkgs.alpinelinux.org/package/edge/community/x86/libzip-dev

### freetype-dev
License: FTL or GPL2+  
https://pkgs.alpinelinux.org/package/edge/main/x86/freetype-dev

### pecl
License: The PHP License  
https://pecl.php.net/copyright.php

### postgresql
License: PostgreSQL  
https://pkgs.alpinelinux.org/package/edge/main/x86/postgresql-dev

## Other CSS and JS libraries
Other components, mostly css and js libraries, that are not dynamically installed.
JS libraries can be found primarily in the `ShopBundle/Resources/scripts/*/plugins/` and `FrameworkBundle/Resources/scripts/*/plugins/` directories.
CSS libraries can be found primarily in the `ShopBundle/Resources/styles/*/libs/` and `FrameworkBundle/Resources/styles/*/libs/` directories.

### Magnific Popup Repository
Library: `FrameworkBundle/Resources/scripts/common/plugins/jquery.magnific-popup.js`  
License: MIT  
https://github.com/dimsemenov/Magnific-Popup/blob/master/LICENSE

### Bootstrap - front-end framework
Library: `FrameworkBundle/Resources/scripts/common/bootstrap/`  
License: MIT  
https://github.com/twbs/bootstrap/blob/master/LICENSE

### Chart.js
Library: `FrameworkBundle/Resources/scripts/admin/plugins/chart.bundle.min.js`  
License: MIT  
https://github.com/chartjs/Chart.js/blob/master/LICENSE.md

### BazingaJsTranslationBundle
Library: `FrameworkBundle/Resources/scripts/common/plugins/BazingaJsTranslationBundle.translator.js`  
License: MIT  
https://github.com/willdurand/BazingaJsTranslationBundle/blob/master/LICENSE

### jQuery Ajax File Uploader Widget
Library: `FrameworkBundle/Resources/scripts/admin/components/jquery.dmuploader.js`  
License: MIT  
https://github.com/danielm/uploader/blob/master/LICENSE.txt

### jquery.fix.clone
Library: `FrameworkBundle/Resources/scripts/common/plugins/jquery.fix.clone.js`  
License: MIT  
https://github.com/spencertipping/jquery.fix.clone/blob/master/README

### jQuery MiniColors: A tiny color picker built on jQuery
Library: `FrameworkBundle/Resources/scripts/admin/plugins/jquery.colorpicker.js`  
License: MIT  
https://github.com/claviska/jquery-minicolors/blob/master/LICENSE.md

### FastClick
Library: `FrameworkBundle/Resources/scripts/common/plugins/fastclick.js`  
License: MIT  
https://github.com/ftlabs/fastclick/blob/master/LICENSE

### hoverIntent jQuery Plugin
Library: `FrameworkBundle/Resources/scripts/admin/plugins/jquery.hoverIntent.js`  
License: MIT  
https://github.com/briancherne/jquery-hoverIntent/blob/master/jquery.hoverIntent.js

### nestedSortable jQuery Plugin
Library: `FrameworkBundle/Resources/scripts/admin/plugins/jquery.mjs.nestedSortable.js`  
License: MIT  
https://github.com/ilikenwf/nestedSortable/blob/master/README.md

### normalize.css
Library: `ShopBundle/Resources/styles/front/common/core/reset.less`  
License: MIT  
https://github.com/necolas/normalize.css/blob/master/LICENSE.md

### jQuery UI Touch Punch 0.2.3
Library: `ShopBundle/Resources/scripts/frontend/plugins/jquery.ui.touch-punch.js`  
License: MIT or GPL Version 2  
https://github.com/furf/jquery-ui-touch-punch/blob/master/jquery.ui.touch-punch.js

### Modernizr
Library: `ShopBundle/Resources/scripts/frontend/plugins/modernizr.js`  
License: MIT  
https://github.com/Modernizr/Modernizr/blob/master/LICENSE

### jquery.cookie
Library: `ShopBundle/Resources/scripts/frontend/plugins/jquery.cookie.js`  
License: MIT  
https://github.com/carhartl/jquery-cookie/blob/master/MIT-LICENSE.txt

## Libraries dynamically referenced via Composer
Run `composer license` in your `shopsys-framework-php-fpm` container of your project to get the latest licensing info about all dependencies. 
For licensing info about all composer dependencies of microservices, run `composer license` in the container of given microservice.

## Libraries dynamically referenced via npm
Run these commands in your `shopsys-framework-php-fpm` container of your project to get the latest licensing info about all packages.

```
cd project-base || true

npm install --no-save license-checker

./node_modules/.bin/license-checker
``` 
## Sources of information about licenses
For the packages installed through the composer, the composer.lock file is the source of the information about licenses. In some cases also the package license information directly in the GitHub repository of the given package is used.

For the packages installed through the npm, the GitHub repositories of these packages are used as the source of the information about licenses.

As a source of information about licenses of images and libraries downloaded and installed through Dockerfile and docker-compose.yml, there are used the GitHub repositories of these images and packages. Licenses of some libraries are mentioned also in a description of used Linux distribution https://pkgs.alpinelinux.org/

Sources of information about licenses of libraries and components that are not downloaded and installed dynamically are the source files of libraries itself or the GitHub repositories of these libraries.

The transitive dependencies of the dependencies and images of 3rd parties are not included.