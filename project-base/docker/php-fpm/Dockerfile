FROM php:7.2-fpm-alpine as base

ARG project_root=.

# install git for computing diffs
RUN apk add --update git

# install Composer
COPY ${project_root}/docker/php-fpm/docker-install-composer /usr/local/bin/docker-install-composer
RUN chmod +x /usr/local/bin/docker-install-composer && docker-install-composer

# libpng-dev needed by "gd" extension
# icu-dev needed by "intl" extension
# postgresql-dev needed by "pgsql" extension
# libzip-dev needed by "zip" extension
# autoconf needed by "redis" extension
# freetype-dev needed by "gd" extension
# libjpeg-turbo-dev needed by "gd" extension
RUN apk add --update \
    libpng-dev \
    icu-dev \
    postgresql-dev \
    libzip-dev \
    autoconf \
    freetype-dev \
    libjpeg-turbo-dev

# "zip" extension warns about deprecation if we do not use a system library
RUN docker-php-ext-configure zip --with-libzip

# "gd" extension needs to have specified jpeg and freetype dir for jpg/jpeg images support
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

# install necessary PHP extensions requested by Composer
RUN docker-php-ext-install \
    bcmath \
    gd \
    intl \
    opcache \
    pgsql \
    pdo_pgsql \
    zip

# redis PHP extension is not provided with the PHP source and must be installed via PECL, build-base used only for installation
RUN apk add --update build-base && pecl install redis-4.0.2 && docker-php-ext-enable redis && apk del build-base

# install npm
RUN apk add --update nodejs-npm

# install grunt-cli using npm to be able to run grunt watch
RUN npm install -g grunt-cli

# install postgresql to allow execution of pg_dump for acceptance tests
RUN apk add --update --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.7/main postgresql

# install locales and switch to en_US.utf8 in order to enable UTF-8 support
# see https://github.com/docker-library/php/issues/240#issuecomment-305038173
RUN apk add --update --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/testing gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php
ENV LC_ALL=en_US.utf8 LANG=en_US.utf8 LANGUAGE=en_US.utf8

# overwrite the original entry-point from the PHP Docker image with our own
COPY ${project_root}/docker/php-fpm/docker-php-entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-php-entrypoint

# copy php.ini configuration
COPY ${project_root}/docker/php-fpm/php-ini-overrides.ini /usr/local/etc/php/php.ini

# the user "www-data" is used when running the image, and therefore should own the workdir
RUN chown www-data:www-data /var/www/html
USER www-data

# allow configuring the GitHub OAuth token
ARG github_oauth_token
RUN if [ -n "$github_oauth_token" ]; then composer config -g github-oauth.github.com $github_oauth_token; fi

# hirak/prestissimo makes the install of Composer dependencies faster by parallel downloading
RUN composer global require hirak/prestissimo

########################################################################################################################

FROM base as production

COPY --chown=www-data:www-data / /var/www/html

RUN touch /var/www/html/PRODUCTION

RUN composer install --optimize-autoloader --no-interaction --no-progress --no-dev

RUN php phing composer npm dirs-create assets

########################################################################################################################

FROM base as ci

# copy all files in the context to /source-code and install Composer dependencies
# the files will be copied inside a mounted volume in /var/www/html so it's available both in php-fpm and nginx container
# see project-base/kubernetes/deployments/webserver-php-fpm.yml for details
COPY --chown=www-data:www-data / /source-code
WORKDIR /source-code
RUN composer install --optimize-autoloader --no-interaction --no-progress

# clean cache because of recreating valid autloading of vendor after /source-code is copied into /var/www/html
RUN php phing composer-dev npm dirs-create test-dirs-create assets standards tests-static tests-acceptance-build clean

# switch back to the original workdir
WORKDIR /var/www/html

########################################################################################################################

FROM base as development

USER root

# allow overwriting UID and GID o the user "www-data" to help solve issues with permissions in mounted volumes
# if the GID is already in use, we will assign GID 82 instead (82 is the standard uid/gid for "www-data" in Alpine)
ARG www_data_uid
ARG www_data_gid
RUN deluser www-data && (addgroup -g $www_data_gid www-data || addgroup -g 82 www-data) && adduser -u $www_data_uid -D -S -G www-data www-data

# as the UID and GID might have changed, change the ownership of the home directory workdir again
RUN chown www-data:www-data -R /home/www-data && chown www-data:www-data /var/www/html

USER www-data
