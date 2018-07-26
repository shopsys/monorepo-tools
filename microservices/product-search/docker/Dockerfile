FROM php:7.2-fpm-alpine

# install Composer
COPY docker-install-composer /usr/local/bin/docker-install-composer
RUN chmod +x /usr/local/bin/docker-install-composer && docker-install-composer

# postgresql-dev needed by "pgsql" extension
RUN apk add --update \
    postgresql-dev

# install necessary PHP extensions requested by Composer
RUN docker-php-ext-install \
    opcache \
    pgsql \
    pdo_pgsql

# install locales and switch to en_US.utf8 in order to enable UTF-8 support
# see https://github.com/docker-library/php/issues/240#issuecomment-305038173
RUN apk add --update --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/testing gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php
ENV LC_ALL=en_US.utf8 LANG=en_US.utf8 LANGUAGE=en_US.utf8

# overwrite the original entry-point from the PHP Docker image with our own
COPY docker-php-entrypoint /usr/local/bin/

# allow overwriting UID and GID o the user "www-data" to help solve issues with permissions in mounted volumes
# if the GID is already in use, we will assign GID 82 instead (82 is the standard uid/gid for "www-data" in Alpine)
ARG www_data_uid
ARG www_data_gid
RUN deluser www-data && (addgroup -g $www_data_gid www-data || addgroup -g 82 www-data) && adduser -u $www_data_uid -D -S -G www-data www-data
USER www-data
