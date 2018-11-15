# Open Source License Acknowledgements and Third-Party Copyrights

Shopsys Framework is licensed under the terms of the [Shopsys Community License](./LICENSE).

Shopsys Framework utilizes third party software from various sources. Portions of this software are copyrighted by their respective owners as indicated in the copyright notices below.

The following acknowledgements pertain to this software license.

## Main components used by Shopsys Framework
These components are installed via `composer` or via `npm`.
You can check all the dependencies using the instructions from the section Libraries dynamically referenced via Composer and Libraries dynamically referenced via npm.

### Symfony Framework and Symfony Components
License: MIT  
https://symfony.com/doc/3.4/contributing/code/license.html
Copyright (c) 2004-2018 Fabien Potencier

### Elasticsearch
License: Apache License 2.0  
https://github.com/elastic/elasticsearch-php/blob/master/LICENSE
Copyright 2013-2014 Elasticsearch

### Grunt: The JavaScript Task Runner
License: MIT  
https://github.com/gruntjs/grunt/blob/master/LICENSE
Copyright jQuery Foundation and other contributors, https://jquery.org/

### Phing
License: LGPL-3.0-only  
https://github.com/phingofficial/phing/blob/master/LICENSE
Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>

### Nette Foundation tools
License: BSD-2-Clause or GPL-2.0 or GPL-3.0  
https://nette.org/en/license

### PHP_CodeSniffer
License: BSD-3-Clause  
https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt
Copyright (c) 2012, Squiz Pty Ltd (ABN 77 084 670 600)

### PHP Parallel Lint
License: BSD-2-Clause  
https://github.com/JakubOnderka/PHP-Parallel-Lint/blob/master/LICENSE
Copyright (c) 2012, Jakub Onderka

### ESLint
License: MIT  
https://github.com/eslint/eslint/blob/master/LICENSE
Copyright JS Foundation and other contributors, https://js.foundation

### jQuery
License: MIT or GPL Version 2  
http://jquery.org/license

### selectize.js
License: Apache License 2.0  
https://github.com/selectize/selectize.js/blob/master/LICENSE
Copyright 2013–2015 Brian Reavis

### slick.js
License: MIT  
https://github.com/kenwheeler/slick/blob/master/LICENSE
Copyright (c) 2013-2016

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
Copyright 2018 Software Freedom Conservancy (SFC)

### Adminer
Image: `Adminer:4.6`  
License: Apache License 2.0 or GPL 2  
https://github.com/vrana/adminer/blob/master/readme.txt

### Elasticsearch
Image: `Docker.elastic.co/elasticsearch/elasticsearch-oss`  
License: Apache License 2.0  
https://github.com/elastic/elasticsearch/blob/66b5ed86f7adede8102cd4d979b9f4924e5bd837/LICENSE.txt
Copyright 2009-2018 Elasticsearch

### Php
Image: `php:7.2-fpm-alpine`  
License: The PHP License  
http://php.net/license/
Copyright (c) 1999 - 2018 The PHP Group. All rights reserved.

### GNU libiconv
Package: `gnu-libiconv`  
License: LGPL  
https://pkgs.alpinelinux.org/package/edge/testing/x86/gnu-libiconv

### Composer - Dependency Management for PHP
License: MIT  
https://github.com/composer/composer/blob/master/LICENSE
Copyright (c) Nils Adermann, Jordi Boggiano

### grunt-cli
License: MIT  
https://github.com/gruntjs/grunt-cli/blob/master/LICENSE-MIT
Copyright (c) 2016 Tyler Kellen, contributors

### nodejs-npm
License: Artistic License 2.0  
https://www.npmjs.com/policies/npm-license
Copyright (c) 2000-2006, The Perl Foundation

### prestissimo (composer plugin)
License: MIT  
https://github.com/hirak/prestissimo/blob/master/LICENSE
Copyright (c) 2017 Hiraku NAKANO

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
Copyright (C) 1999-2018 Dieter Baron and Thomas Klausner

### freetype-dev
License: FTL or GPL2+  
https://pkgs.alpinelinux.org/package/edge/main/x86/freetype-dev
Copyright 1996-2002, 2006 by David Turner, Robert Wilhelm, and Werner Lemberg

### pecl
License: The PHP License  
https://pecl.php.net/copyright.php
Copyright © 2001-2018 The PHP Group. All rights reserved.

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
Copyright (c) 2014-2016 Dmitry Semenov, http://dimsemenov.com

### Bootstrap - front-end framework
Library: `FrameworkBundle/Resources/scripts/common/bootstrap/`  
License: MIT  
https://github.com/twbs/bootstrap/blob/master/LICENSE
Copyright (c) 2011-2018 Twitter, Inc.

### Chart.js
Library: `FrameworkBundle/Resources/scripts/admin/plugins/chart.bundle.min.js`  
License: MIT  
https://github.com/chartjs/Chart.js/blob/master/LICENSE.md
Copyright (c) 2018 Chart.js Contributors

### BazingaJsTranslationBundle
Library: `FrameworkBundle/Resources/scripts/common/plugins/BazingaJsTranslationBundle.translator.js`  
License: MIT  
https://github.com/willdurand/BazingaJsTranslationBundle/blob/master/LICENSE
Copyright (c) William Durand <william.durand1@gmail.com>

### jQuery Ajax File Uploader Widget
Library: `FrameworkBundle/Resources/scripts/admin/components/jquery.dmuploader.js`  
License: MIT  
https://github.com/danielm/uploader/blob/master/LICENSE.txt
Copyright © Daniel Morales, https://www.danielmg.org

### jquery.fix.clone
Library: `FrameworkBundle/Resources/scripts/common/plugins/jquery.fix.clone.js`  
License: MIT  
https://github.com/spencertipping/jquery.fix.clone/blob/master/README

### jQuery MiniColors: A tiny color picker built on jQuery
Library: `FrameworkBundle/Resources/scripts/admin/plugins/jquery.colorpicker.js`  
License: MIT  
https://github.com/claviska/jquery-minicolors/blob/master/LICENSE.md
Copyright 2017 A Beautiful Site, LLC

### FastClick
Library: `FrameworkBundle/Resources/scripts/common/plugins/fastclick.js`  
License: MIT  
https://github.com/ftlabs/fastclick/blob/master/LICENSE
Copyright (c) 2014 The Financial Times Ltd.

### hoverIntent jQuery Plugin
Library: `FrameworkBundle/Resources/scripts/admin/plugins/jquery.hoverIntent.js`  
License: MIT  
https://github.com/briancherne/jquery-hoverIntent/blob/master/jquery.hoverIntent.js
Copyright 2007-2017 Brian Cherne

### nestedSortable jQuery Plugin
Library: `FrameworkBundle/Resources/scripts/admin/plugins/jquery.mjs.nestedSortable.js`  
License: MIT  
https://github.com/ilikenwf/nestedSortable/blob/master/README.md

### normalize.css
Library: `ShopBundle/Resources/styles/front/common/core/reset.less`  
License: MIT  
https://github.com/necolas/normalize.css/blob/master/LICENSE.md
Copyright © Nicolas Gallagher and Jonathan Neal

### jQuery UI Touch Punch 0.2.3
Library: `ShopBundle/Resources/scripts/frontend/plugins/jquery.ui.touch-punch.js`  
License: MIT or GPL Version 2  
https://github.com/furf/jquery-ui-touch-punch/blob/master/jquery.ui.touch-punch.js
Copyright 2011–2014, Dave Furfero

### Modernizr
Library: `ShopBundle/Resources/scripts/frontend/plugins/modernizr.js`  
License: MIT  
https://github.com/Modernizr/Modernizr/blob/master/LICENSE

### jquery.cookie
Library: `ShopBundle/Resources/scripts/frontend/plugins/jquery.cookie.js`  
License: MIT  
https://github.com/carhartl/jquery-cookie/blob/master/MIT-LICENSE.txt
Copyright 2014 Klaus Hartl

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
